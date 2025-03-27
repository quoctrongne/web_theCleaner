// JavaScript for booking.php

// Flag để kiểm tra trạng thái tải API
let isGoogleMapsAPILoaded = false;

// Hàm kiểm tra và chờ API tải xong
function waitForGoogleMapsAPI() {
    return new Promise((resolve, reject) => {
        const checkInterval = setInterval(() => {
            if (typeof google !== 'undefined' && google.maps && google.maps.places) {
                clearInterval(checkInterval);
                isGoogleMapsAPILoaded = true;
                resolve();
            }
        }, 100);

        // Timeout sau 10 giây
        setTimeout(() => {
            clearInterval(checkInterval);
            reject(new Error('Google Maps API tải quá chậm'));
        }, 10000);
    });
}

// Hàm callback cho Google Maps API
function initPlacesAPI() {
    console.log("Google Maps Places API loaded");
    isGoogleMapsAPILoaded = true;
}

// Đảm bảo hàm callback có sẵn trong window
window.initPlacesAPI = initPlacesAPI;

// Set up autocomplete for address input
    // Xử lý sự kiện khi địa chỉ được chọn
    autocomplete.addListener("place_changed", () => {
        const place = autocomplete.getPlace();
        
        if (!place.geometry) {
            console.log("Không tìm thấy thông tin địa chỉ");
            return;
        }
        
        // Lưu thông tin địa chỉ
        document.getElementById("formattedAddress").value = place.formatted_address || addressInput.value;
        document.getElementById("latitude").value = place.geometry.location.lat();
        document.getElementById("longitude").value = place.geometry.location.lng();
        
        console.log("Địa chỉ đã chọn:", place.formatted_address);
    });
    document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            const name = document.getElementById('bookName').value;
            const email = document.getElementById('bookEmail').value;
            const phone = document.getElementById('bookPhone').value;
            const address = document.getElementById('bookAddress').value;
            const service = document.getElementById('bookService').value;
            const date = document.getElementById('bookDate').value;
            const time = document.getElementById('bookTime').value;
            const area = document.getElementById('bookArea').value;
            
            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Vui lòng nhập đúng định dạng email.');
                e.preventDefault();
                return;
            }
            
            // Validate phone
            const phoneRegex = /^(\+84|0)[3|5|7|8|9][0-9]{8}$/;
            if (!phoneRegex.test(phone)) {
                alert('Vui lòng nhập đúng số điện thoại Việt Nam.');
                e.preventDefault();
                return;
            }
            
            // Validate date
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                alert('Vui lòng chọn ngày trong tương lai.');
                e.preventDefault();
                return;
            }
        });
    }
});
    // Add event listener for input changes
    addressInput.addEventListener('input', function() {
        const input = this.value;
        
        if (input.length > 2) {
            // Request predictions from the AutocompleteService
            autocompleteService.getPlacePredictions({
                input: input,
                componentRestrictions: { country: 'vn' }, 
                types: ['address']
            }, displaySuggestions);
        } else {
            suggestionsContainer.style.display = 'none';
        }
    });
    
    // Display suggestions in the dropdown
    function displaySuggestions(predictions, status) {
        suggestionsContainer.innerHTML = '';
        
        if (status !== google.maps.places.PlacesServiceStatus.OK || !predictions) {
            suggestionsContainer.style.display = 'none';
            return;
        }
        
        predictions.forEach(prediction => {
            const item = document.createElement('div');
            item.className = 'suggestion-item';
            
            const mainText = document.createElement('div');
            mainText.className = 'main-text';
            mainText.textContent = prediction.structured_formatting.main_text;
            
            const secondaryText = document.createElement('div');
            secondaryText.className = 'secondary-text';
            secondaryText.textContent = prediction.structured_formatting.secondary_text || '';
            
            item.appendChild(mainText);
            item.appendChild(secondaryText);
            
            item.addEventListener('click', () => {
                addressInput.value = prediction.description;
                
                // Get place details
                placesService.getDetails({
                    placeId: prediction.place_id,
                    fields: ['formatted_address', 'geometry']
                }, (place, status) => {
                    if (status === google.maps.places.PlacesServiceStatus.OK) {
                        // Store formatted address and coordinates
                        document.getElementById('formattedAddress').value = place.formatted_address;
                        document.getElementById('latitude').value = place.geometry.location.lat();
                        document.getElementById('longitude').value = place.geometry.location.lng();
                        
                        console.log("Selected address:", place.formatted_address);
                        console.log("Coordinates:", place.geometry.location.lat(), place.geometry.location.lng());
                    }
                });
                
                suggestionsContainer.style.display = 'none';
            });
            
            suggestionsContainer.appendChild(item);
        });
        
        suggestionsContainer.style.display = 'block';
    }
    
    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target !== addressInput && !suggestionsContainer.contains(e.target)) {
            suggestionsContainer.style.display = 'none';
        }
    });


// Set up event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', async function() {
    console.log("DOM fully loaded");
    
    // Thiết lập ngày tối thiểu cho đặt lịch (ngày mai)
    const dateInput = document.getElementById('bookDate');
    if (dateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        dateInput.setAttribute('min', tomorrowStr);
    }
    
    // Booking form validation and submission
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            // Basic validation
            const name = document.getElementById('bookName').value;
            const email = document.getElementById('bookEmail').value;
            const phone = document.getElementById('bookPhone').value;
            const address = document.getElementById('bookAddress').value;
            const formattedAddress = document.getElementById('formattedAddress').value;
            const service = document.getElementById('bookService').value;
            const date = document.getElementById('bookDate').value;
            const time = document.getElementById('bookTime').value;
            const area = document.getElementById('bookArea').value;
            
            // Check if all required fields are filled
            if (!name || !email || !phone || !address || !service || !date || !time || !area) {
                alert('Vui lòng điền đầy đủ thông tin.');
                e.preventDefault();
                return;
            }
            
            // Check if address has been selected from suggestions
            if (!formattedAddress) {
                alert('Vui lòng chọn một địa chỉ từ danh sách gợi ý.');
                e.preventDefault();
                return;
            }
            
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Vui lòng nhập đúng định dạng email.');
                e.preventDefault();
                return;
            }
            
            // Validate phone number (simple validation for Vietnamese phone numbers)
            const phoneRegex = /^(\+84|0)[3|5|7|8|9][0-9]{8}$/;
            if (!phoneRegex.test(phone)) {
                alert('Vui lòng nhập đúng số điện thoại Việt Nam.');
                e.preventDefault();
                return;
            }
            
            // Validate date (must be a future date)
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                alert('Vui lòng chọn ngày trong tương lai.');
                e.preventDefault();
                return;
            }
            
            // Form submission will continue if all validations pass
            console.log({
                name,
                email,
                phone,
                address: formattedAddress,
                location: {
                    lat: document.getElementById('latitude').value,
                    lng: document.getElementById('longitude').value
                },
                service,
                date,
                time,
                area,
                note: document.getElementById('bookNote').value
            });
            
            // Form will be submitted normally since we're not calling e.preventDefault() here
        });
    }
    
    
});