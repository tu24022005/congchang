/* cart/checkout.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
    // 1. TỰ ĐỘNG ĐIỀN ĐỊA CHỈ ĐÃ LƯU
    const savedAddressSelect = document.getElementById('saved-address');
    const nameInput = document.querySelector('[name="customer_name"]');
    const phoneInput = document.querySelector('[name="customer_phone"]');
    const addressInput = document.getElementById('checkout-customer-address');

    if (savedAddressSelect) {
        const fillSavedAddress = () => {
            const selected = savedAddressSelect.options[savedAddressSelect.selectedIndex];
            if (selected && selected.value) {
                if (selected.dataset.name) nameInput.value = selected.dataset.name;
                if (selected.dataset.phone) phoneInput.value = selected.dataset.phone;
                if (selected.dataset.address) addressInput.value = selected.dataset.address;
            }
        };
        savedAddressSelect.addEventListener('change', fillSavedAddress);
    }

    // 2. TÍNH PHÍ VẬN CHUYỂN DYNAMIC
    const zoneSelect = document.getElementById('shipping-zone');
    const providerSelect = document.getElementById('shipping-provider');
    const shippingFeeDisplay = document.getElementById('checkout-shipping-fee');
    const finalTotalDisplay = document.getElementById('checkout-final-total');
    const baseSubtotal = Number(0);
    const voucherDiscount = Number(0);
    const hasShippingVoucher = Boolean(0);
    const formatMoney = val => new Intl.NumberFormat('vi-VN').format(Math.round(val)) + ' đ';

    function refreshShippingFee() {
        const zoneVal = zoneSelect?.value;
        const selectedZoneOpt = zoneSelect?.options[zoneSelect.selectedIndex];
        const selectedProviderOpt = providerSelect?.options[providerSelect.selectedIndex];

        // Cập nhật nhãn phí trong dropdown đơn vị vận chuyển
        if (providerSelect && zoneVal) {
            providerSelect.querySelectorAll('option[data-fees]').forEach(opt => {
                const fees = JSON.parse(opt.dataset.fees || '{}');
                opt.textContent = opt.dataset.label + (fees[zoneVal] ? ' - ' + formatMoney(fees[zoneVal]) : '');
            });
        }

        const providerFees = selectedProviderOpt?.dataset.fees ? JSON.parse(selectedProviderOpt.dataset.fees) : {};
        let fee = Number(providerFees[zoneVal] || selectedZoneOpt?.dataset.fee || 0);

        if (hasShippingVoucher) {
            fee = 0;
            if (shippingFeeDisplay) shippingFeeDisplay.textContent = 'Miễn phí (Voucher)';
        } else {
            if (shippingFeeDisplay) {
                shippingFeeDisplay.textContent = fee > 0 ? formatMoney(fee) : 'Chọn khu vực & ĐVVC';
            }
        }

        const finalTotal = Math.max(0, baseSubtotal - voucherDiscount + fee);
        if (finalTotalDisplay) {
            finalTotalDisplay.textContent = formatMoney(finalTotal);
        }
    }

    zoneSelect?.addEventListener('change', refreshShippingFee);
    providerSelect?.addEventListener('change', refreshShippingFee);
    refreshShippingFee();

    // 3. TÍCH HỢP TỈNH / THÀNH, QUẬN / HUYỆN, PHƯỜNG / XÃ & BẢN ĐỒ ĐỒNG BỘ
    const addressWrapper = document.querySelector('[data-vn-address]');
    const provinceSelect = addressWrapper?.querySelector('[data-province]');
    const districtSelect = addressWrapper?.querySelector('[data-district]');
    const wardSelect = addressWrapper?.querySelector('[data-ward]');
    const api = 'https://provinces.open-api.vn/api';

    const fillSelect = (select, items, placeholder) => {
        if (!select) return;
        select.innerHTML = `<option value="">${placeholder}</option>` + items.map(item => `<option value="${item.code}" data-name="${item.name}">${item.name}</option>`).join('');
        select.disabled = false;
    };

    // Tải danh sách tỉnh/thành ban đầu
    if (provinceSelect) {
        fetch(`${api}/p/`).then(res => res.json()).then(items => {
            window._provincesList = items;
            fillSelect(provinceSelect, items, 'Tỉnh/Thành phố');
        }).catch(err => console.error('Lỗi tải tỉnh/thành:', err));

        provinceSelect.addEventListener('change', function () {
            if (!districtSelect || !wardSelect) return;
            districtSelect.innerHTML = '<option value="">Đang tải...</option>';
            districtSelect.disabled = true;
            wardSelect.innerHTML = '<option value="">Phường/Xã</option>';
            wardSelect.disabled = true;
            if (this.value) {
                fetch(`${api}/p/${this.value}?depth=2`).then(res => res.json()).then(data => {
                    fillSelect(districtSelect, data.districts || [], 'Quận/Huyện');
                }).catch(() => {});
            }
        });

        districtSelect?.addEventListener('change', function () {
            if (!wardSelect) return;
            wardSelect.innerHTML = '<option value="">Đang tải...</option>';
            wardSelect.disabled = true;
            if (this.value) {
                fetch(`${api}/d/${this.value}?depth=2`).then(res => res.json()).then(data => {
                    fillSelect(wardSelect, data.wards || [], 'Phường/Xã');
                }).catch(() => {});
            }
        });
    }

    // Hàm chuẩn hóa chuỗi tiếng Việt để so khớp địa danh
    const norm = str => (str || '').toString().toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[đĐ]/g, 'd')
        .replace(/[^a-z0-9]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();

    const strip = str => norm(str)
        .replace(/^(tinh|thanh pho|tp|quan|huyen|thi xa|tx|phuong|xa|thi tran|tt)\s+/gi, '')
        .trim();

    // Hàm tự động chọn Tỉnh/Thành, Quận/Huyện, Phường/Xã từ thông tin định vị
    async function syncAddressFromGeocode(addr, fullAddress) {
        if (!provinceSelect || !districtSelect || !wardSelect) return;

        const fullNorm = norm(fullAddress);

        // 1. TÌM VÀ CHỌN TỈNH / THÀNH PHỐ
        if (!window._provincesList || !window._provincesList.length) {
            try {
                const res = await fetch(`${api}/p/`);
                window._provincesList = await res.json();
                fillSelect(provinceSelect, window._provincesList, 'Tỉnh/Thành phố');
            } catch (e) {
                return;
            }
        }

        const provList = window._provincesList;
        const provCandidates = [
            strip(addr.city),
            strip(addr.province),
            strip(addr.state)
        ].filter(Boolean);

        let matchedProv = null;
        for (const p of provList) {
            const pStrip = strip(p.name);
            if (provCandidates.some(c => c === pStrip || c.includes(pStrip) || pStrip.includes(c))) {
                matchedProv = p;
                break;
            }
        }
        if (!matchedProv) {
            for (const p of provList) {
                const pStrip = strip(p.name);
                if (pStrip.length > 2 && fullNorm.includes(pStrip)) {
                    matchedProv = p;
                    break;
                }
            }
        }

        if (!matchedProv) return;
        provinceSelect.value = matchedProv.code;

        // 2. TẢI VÀ CHỌN QUẬN / HUYỆN
        districtSelect.innerHTML = '<option value="">Đang tải...</option>';
        districtSelect.disabled = true;
        wardSelect.innerHTML = '<option value="">Phường/Xã</option>';
        wardSelect.disabled = true;

        let districtList = [];
        try {
            const res = await fetch(`${api}/p/${matchedProv.code}?depth=2`);
            const data = await res.json();
            districtList = data.districts || [];
            fillSelect(districtSelect, districtList, 'Quận/Huyện');
        } catch (e) {
            return;
        }

        const distCandidates = [
            strip(addr.city_district),
            strip(addr.county),
            strip(addr.district),
            strip(addr.suburb),
            strip(addr.town)
        ].filter(Boolean);

        let matchedDist = null;
        for (const d of districtList) {
            const dStrip = strip(d.name);
            if (distCandidates.some(c => c === dStrip || c.includes(dStrip) || dStrip.includes(c))) {
                matchedDist = d;
                break;
            }
        }
        if (!matchedDist) {
            for (const d of districtList) {
                const dStrip = strip(d.name);
                if (dStrip.length > 2 && fullNorm.includes(dStrip)) {
                    matchedDist = d;
                    break;
                }
            }
        }

        // Trường hợp Nominatim trả về phường trong suburb mà không có quận (như Phú Diễn)
        const wardRaw = addr.quarter || addr.suburb || addr.ward || addr.neighbourhood;
        if (!matchedDist && wardRaw) {
            try {
                const wSearchRes = await fetch(`${api}/w/search/?q=${encodeURIComponent(wardRaw)}`);
                const foundWards = await wSearchRes.json();
                if (Array.isArray(foundWards)) {
                    const targetW = strip(wardRaw);
                    for (const fw of foundWards) {
                        if (strip(fw.name) === targetW) {
                            const foundD = districtList.find(item => item.code === fw.district_code);
                            if (foundD) {
                                matchedDist = foundD;
                                break;
                            }
                        }
                    }
                }
            } catch (e) {}
        }

        if (!matchedDist) return;
        districtSelect.value = matchedDist.code;

        // 3. TẢI VÀ CHỌN PHƯỜNG / XÃ
        wardSelect.innerHTML = '<option value="">Đang tải...</option>';
        wardSelect.disabled = true;

        let wardList = [];
        try {
            const res = await fetch(`${api}/d/${matchedDist.code}?depth=2`);
            const data = await res.json();
            wardList = data.wards || [];
            fillSelect(wardSelect, wardList, 'Phường/Xã');
        } catch (e) {
            return;
        }

        const wardCandidates = [
            strip(addr.quarter),
            strip(addr.suburb),
            strip(addr.ward),
            strip(addr.neighbourhood),
            strip(addr.village)
        ].filter(Boolean);

        let matchedWard = null;
        for (const w of wardList) {
            const wStrip = strip(w.name);
            if (wardCandidates.some(c => c === wStrip || c.includes(wStrip) || wStrip.includes(c))) {
                matchedWard = w;
                break;
            }
        }
        if (!matchedWard) {
            for (const w of wardList) {
                const wStrip = strip(w.name);
                if (wStrip.length > 2 && fullNorm.includes(wStrip)) {
                    matchedWard = w;
                    break;
                }
            }
        }

        if (matchedWard) {
            wardSelect.value = matchedWard.code;
        }

        // 4. TỰ ĐỘNG CHỌN KHU VỰC GIAO HÀNG & TÍNH PHÍ SHIP
        if (zoneSelect) {
            const isInner = fullNorm.includes('ha noi') || fullNorm.includes('hanoi') ||
                            fullNorm.includes('ho chi minh') || fullNorm.includes('sai gon');
            const targetZone = isInner ? 'inner_city' : 'other_city';
            if (zoneSelect.value !== targetZone) {
                zoneSelect.value = targetZone;
                zoneSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    }

    // 4. BẢN ĐỒ VỊ TRÍ GIAO HÀNG LEAFLET & ĐỊNH VỊ
    const mapElement = document.getElementById('delivery-map');
    if (mapElement && typeof L !== 'undefined') {
        const latInput = document.getElementById('delivery-latitude');
        const lngInput = document.getElementById('delivery-longitude');
        const mapStatus = document.getElementById('map-status');
        const searchInput = document.getElementById('map-search');
        const searchBtn = document.getElementById('map-search-button');
        const geoBtn = document.getElementById('use-current-location');

        const initLat = Number(latInput?.value) || 21.0285;
        const initLng = Number(lngInput?.value) || 105.8542;

        const map = L.map('delivery-map').setView([initLat, initLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);

        // Hàm xử lý toạ độ: ghim vị trí + reverse geocode lấy địa chỉ chi tiết và tự chọn dropdowns
        async function reverseGeocodeAndSync(lat, lng) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 16);
            if (latInput) latInput.value = Number(lat).toFixed(7);
            if (lngInput) lngInput.value = Number(lng).toFixed(7);

            if (mapStatus) {
                mapStatus.textContent = 'Đang nhận diện địa chỉ từ toạ độ...';
                mapStatus.className = 'small text-primary ms-3';
            }

            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&addressdetails=1`, {
                    headers: { 'Accept-Language': 'vi' }
                });
                if (!response.ok) throw new Error('Không thể kết nối dịch vụ bản đồ.');
                const data = await response.json();

                const addr = data.address || {};
                const displayName = data.display_name || '';

                // Cập nhật ô nhập địa chỉ chi tiết
                if (addressInput && displayName) {
                    addressInput.value = displayName;
                    addressInput.dispatchEvent(new Event('input', { bubbles: true }));
                }

                // Cập nhật ô tìm kiếm bản đồ
                if (searchInput && displayName) {
                    searchInput.value = displayName;
                }

                // Tự động khớp và chọn Tỉnh/Thành, Quận/Huyện, Phường/Xã
                await syncAddressFromGeocode(addr, displayName);

                if (mapStatus) {
                    mapStatus.textContent = '✓ Đã cập nhật toạ độ và địa chỉ nhận hàng!';
                    mapStatus.className = 'small text-success ms-3 fw-semibold';
                }
            } catch (err) {
                console.warn('Lỗi reverse geocode:', err);
                if (mapStatus) {
                    mapStatus.textContent = 'Đã ghim toạ độ. Vui lòng chọn Tỉnh/Huyện/Xã nếu chưa tự điền.';
                    mapStatus.className = 'small text-warning ms-3';
                }
            }
        }

        // Khi kéo thả marker trên bản đồ
        marker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            reverseGeocodeAndSync(pos.lat, pos.lng);
        });

        // Khi click chuột vào bất kỳ điểm nào trên bản đồ
        map.on('click', function (e) {
            reverseGeocodeAndSync(e.latlng.lat, e.latlng.lng);
        });

        // Tìm kiếm địa chỉ bằng ô tìm kiếm
        if (searchBtn && searchInput) {
            const handleSearch = function () {
                const query = searchInput.value.trim();
                if (!query) return;
                if (mapStatus) {
                    mapStatus.textContent = 'Đang tìm địa chỉ...';
                    mapStatus.className = 'small text-primary ms-3';
                }
                fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&addressdetails=1&countrycodes=vn&q=${encodeURIComponent(query)}`, {
                    headers: { 'Accept-Language': 'vi' }
                })
                .then(res => res.json())
                .then(results => {
                    if (results && results.length > 0) {
                        const first = results[0];
                        reverseGeocodeAndSync(Number(first.lat), Number(first.lon));
                    } else {
                        if (mapStatus) {
                            mapStatus.textContent = 'Không tìm thấy vị trí phù hợp.';
                            mapStatus.className = 'small text-warning ms-3';
                        }
                    }
                })
                .catch(() => {
                    if (mapStatus) {
                        mapStatus.textContent = 'Lỗi tìm kiếm bản đồ.';
                        mapStatus.className = 'small text-danger ms-3';
                    }
                });
            };

            searchBtn.addEventListener('click', handleSearch);
            searchInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSearch();
                }
            });
        }

        // Bấm nút "Lấy vị trí hiện tại của tôi"
        if (geoBtn && navigator.geolocation) {
            geoBtn.addEventListener('click', function () {
                if (mapStatus) {
                    mapStatus.textContent = 'Đang lấy toạ độ GPS...';
                    mapStatus.className = 'small text-primary ms-3';
                }
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        reverseGeocodeAndSync(pos.coords.latitude, pos.coords.longitude);
                    },
                    err => {
                        console.error('Geolocation error:', err);
                        if (mapStatus) {
                            mapStatus.textContent = 'Không thể lấy vị trí hiện tại. Vui lòng kiểm tra quyền truy cập vị trí trên trình duyệt.';
                            mapStatus.className = 'small text-danger ms-3';
                        }
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            });
        }
    }

    // Chuẩn hóa địa chỉ khi gửi form
    const checkoutForm = document.getElementById('checkout-order-form');
    checkoutForm?.addEventListener('submit', function () {
        if (!provinceSelect || !districtSelect || !wardSelect || !addressInput) return;
        const names = [wardSelect, districtSelect, provinceSelect].map(s => s.options[s.selectedIndex]?.dataset.name).filter(Boolean);
        const street = addressInput.value.split(',').map(p => p.trim()).filter(Boolean)[0] || addressInput.value.trim();
        if (names.length === 3 && street && !addressInput.value.includes(names[0])) {
            addressInput.value = [street, ...names].join(', ');
        }
    });
});

