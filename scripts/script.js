// JavaScript cho tất cả các trang
// JavaScript for booking.html

// Biến toàn cục để lưu trữ dịch vụ Places
let placesService;
let autocompleteService;

// Hàm kiểm tra và khởi tạo Google Maps API
function checkGoogleMapsAPI() {
    if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
        console.error("Google Maps Places API chưa được tải đầy đủ");
        // Thử tải lại sau 1 giây
        setTimeout(checkGoogleMapsAPI, 1000);
        return false;
    }
    return true;
}

// Hàm callback cho Google Maps API
function initPlacesAPI() {
    console.log("Bắt đầu khởi tạo Google Maps Places API");
    
    // Kiểm tra API đã tải chưa
    if (!checkGoogleMapsAPI()) {
        return;
    }
    
    try {
        // Thiết lập tự động hoàn thành
        const input = document.getElementById("bookAddress");
        const suggestionsContainer = document.getElementById('address-suggestions');
        
        if (!input || !suggestionsContainer) {
            console.error("Không tìm thấy các phần tử DOM cần thiết");
            return;
        }
        
        // Tạo một phần tử ẩn để sử dụng PlacesService
        const placesDiv = document.createElement('div');
        placesDiv.style.display = 'none';
        placesDiv.id = 'map-placeholder';
        document.body.appendChild(placesDiv);
        
        // Khởi tạo dịch vụ Places
        placesService = new google.maps.places.PlacesService(placesDiv);
        autocompleteService = new google.maps.places.AutocompleteService();
        
        // Cấu hình Autocomplete
        const autocompleteOptions = {
            componentRestrictions: { country: "vn" },
            types: ["address"]
        };
        
        const autocomplete = new google.maps.places.Autocomplete(input, autocompleteOptions);
        
        // Xử lý sự kiện khi địa chỉ được chọn
        autocomplete.addListener("place_changed", () => {
            const place = autocomplete.getPlace();
            
            if (!place.geometry) {
                console.log("Không tìm thấy thông tin địa chỉ");
                return;
            }
            
            // Lưu thông tin địa chỉ
            document.getElementById("formattedAddress").value = place.formatted_address || input.value;
            document.getElementById("latitude").value = place.geometry.location.lat();
            document.getElementById("longitude").value = place.geometry.location.lng();
            
            console.log("Địa chỉ đã chọn:", place.formatted_address);
        });
        
        // Tự động gợi ý địa chỉ
        input.addEventListener('input', function() {
            const inputValue = this.value;
            
            if (inputValue.length > 2) {
                autocompleteService.getPlacePredictions({
                    input: inputValue,
                    componentRestrictions: { country: 'vn' },
                    types: ['address']
                }, (predictions, status) => {
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
                            input.value = prediction.description;
                            
                            // Lấy chi tiết địa điểm
                            placesService.getDetails({
                                placeId: prediction.place_id,
                                fields: ['formatted_address', 'geometry']
                            }, (place, status) => {
                                if (status === google.maps.places.PlacesServiceStatus.OK) {
                                    document.getElementById('formattedAddress').value = place.formatted_address;
                                    document.getElementById('latitude').value = place.geometry.location.lat();
                                    document.getElementById('longitude').value = place.geometry.location.lng();
                                    
                                    console.log("Địa chỉ được chọn:", place.formatted_address);
                                }
                            });
                            
                            suggestionsContainer.style.display = 'none';
                        });
                        
                        suggestionsContainer.appendChild(item);
                    });
                    
                    suggestionsContainer.style.display = 'block';
                });
            } else {
                suggestionsContainer.style.display = 'none';
            }
        });
        
        // Đóng gợi ý khi click ngoài
        document.addEventListener('click', function(e) {
            if (e.target !== input && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.style.display = 'none';
            }
        });
        
        console.log("Khởi tạo Google Maps Places API thành công");
    } catch (error) {
        console.error("Lỗi khi khởi tạo Google Maps Places API:", error);
    }
}

// Đảm bảo hàm callback có sẵn trong window
window.initPlacesAPI = initPlacesAPI;

// Sự kiện khi DOM được tải
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM đã tải xong");
    
    // Thiết lập ngày tối thiểu cho đặt lịch (ngày mai)
    const dateInput = document.getElementById('bookDate');
    if (dateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        dateInput.setAttribute('min', tomorrowStr);
    }
    
    // Xử lý form đặt lịch
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Lấy giá trị từ form
            const name = document.getElementById('bookName').value;
            const email = document.getElementById('bookEmail').value;
            const phone = document.getElementById('bookPhone').value;
            const address = document.getElementById('bookAddress').value;
            const formattedAddress = document.getElementById('formattedAddress').value;
            const service = document.getElementById('bookService').value;
            const date = document.getElementById('bookDate').value;
            const time = document.getElementById('bookTime').value;
            const area = document.getElementById('bookArea').value;
            
            // Kiểm tra các trường bắt buộc
            const requiredFields = [name, email, phone, address, service, date, time, area];
            if (requiredFields.some(field => !field)) {
                alert('Vui lòng điền đầy đủ thông tin.');
                return;
            }
            
            // Kiểm tra địa chỉ đã được chọn từ gợi ý
            if (!formattedAddress) {
                alert('Vui lòng chọn một địa chỉ từ danh sách gợi ý.');
                return;
            }
            
            // Kiểm tra email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Vui lòng nhập đúng định dạng email.');
                return;
            }
            
            // Kiểm tra số điện thoại
            const phoneRegex = /^(\+84|0)[3|5|7|8|9][0-9]{8}$/;
            if (!phoneRegex.test(phone)) {
                alert('Vui lòng nhập đúng số điện thoại Việt Nam.');
                return;
            }
            
            // Kiểm tra ngày
            const selectedDate = new Date(date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                alert('Vui lòng chọn ngày trong tương lai.');
                return;
            }
            
            // Thông báo thành công
            alert('Cảm ơn bạn đã đặt lịch! Chúng tôi sẽ liên hệ xác nhận trong thời gian sớm nhất.');
            bookingForm.reset();
            
            // Trong một ứng dụng thực tế, bạn sẽ gửi dữ liệu này đến máy chủ
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
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const menuBtn = document.getElementById('menuBtn');
    const navMenu = document.getElementById('navMenu');
    
    if (menuBtn && navMenu) {
        menuBtn.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            
            if (menuBtn.innerHTML === '<i class="fas fa-bars"></i>') {
                menuBtn.innerHTML = '<i class="fas fa-times"></i>';
            } else {
                menuBtn.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });
    }
    
    // Smooth Scrolling cho các anchor link
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                e.preventDefault();
                
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                
                if (navMenu && navMenu.classList.contains('active')) {
                    navMenu.classList.remove('active');
                    if (menuBtn) {
                        menuBtn.innerHTML = '<i class="fas fa-bars"></i>';
                    }
                }
            }
        });
    });
    
    // Fixed Header on Scroll
    window.addEventListener('scroll', () => {
        const header = document.querySelector('.header');
        if (header) {
            if (window.scrollY > 50) {
                header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            } else {
                header.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
            }
        }
    });
    
    // Xử lý Form Contact
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.');
            contactForm.reset();
        });
    }
    
    // Xử lý Form Booking
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Cảm ơn bạn đã đặt lịch! Chúng tôi sẽ liên hệ xác nhận trong thời gian sớm nhất.');
            bookingForm.reset();
        });
    }
    
    // Xử lý Form Testimonial
    const testimonialForm = document.getElementById('testimonialForm');
    if (testimonialForm) {
        testimonialForm.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Cảm ơn bạn đã chia sẻ đánh giá! Đánh giá của bạn sẽ được hiển thị sau khi xét duyệt.');
            testimonialForm.reset();
        });
    }
    
    // Xử lý Star Rating
    const starRating = document.querySelector('.star-rating');
    if (starRating) {
        const stars = starRating.querySelectorAll('i');
        
        stars.forEach(star => {
            star.addEventListener('click', () => {
                const rating = parseInt(star.getAttribute('data-rating'));
                
                stars.forEach(s => {
                    const sRating = parseInt(s.getAttribute('data-rating'));
                    
                    if (sRating <= rating) {
                        s.classList.remove('far');
                        s.classList.add('fas');
                    } else {
                        s.classList.remove('fas');
                        s.classList.add('far');
                    }
                });
            });
        });
    }
    
    // Testimonial Slider (trang chủ và trang testimonials)
    setupTestimonialSlider();
    
    // Stats Counter Animation
    const statItems = document.querySelectorAll('.stat-item h3, .stat h3');
    
    if (statItems.length > 0) {
        function animateCounter(el) {
            const target = parseInt(el.innerText.replace(/[^\d]/g, ''));
            const suffix = el.innerText.match(/[^\d]+/) ? el.innerText.match(/[^\d]+/)[0] : '';
            let count = 0;
            const duration = 2000; // 2 seconds
            const frameDuration = 1000 / 60; // 60fps
            const totalFrames = Math.round(duration / frameDuration);
            const increment = target / totalFrames;
            
            const timer = setInterval(() => {
                count += increment;
                
                if (count >= target) {
                    el.innerText = target + suffix;
                    clearInterval(timer);
                } else {
                    el.innerText = Math.floor(count) + suffix;
                }
            }, frameDuration);
        }
        
        // Intersection Observer for Stats Section
        const statsSection = document.querySelector('.stats, .testimonial-stats');
        
        if (statsSection) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        statItems.forEach(item => {
                            animateCounter(item);
                        });
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            
            observer.observe(statsSection);
        }
    }
    
    // Xử lý FAQ Accordion
    const faqItems = document.querySelectorAll('.faq-item');
    
    if (faqItems.length > 0) {
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            
            question.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                
                // Đóng tất cả các item khác
                faqItems.forEach(otherItem => {
                    otherItem.classList.remove('active');
                });
                
                // Toggle active class cho item hiện tại
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    }
    
    // Initialize page based on current page
    initCurrentPage();
});

// Testimonial Slider Function
function setupTestimonialSlider() {
    // Xử lý slider cho phần testimonial ở trang chủ
    const homeTestimonialContainer = document.querySelector('.featured-testimonial');
    if (homeTestimonialContainer) {
        const testimonials = homeTestimonialContainer.querySelectorAll('.testimonial');
        if (testimonials.length > 1) {
            // Tạo các dots nếu chưa có
            let dotsContainer = homeTestimonialContainer.querySelector('.testimonial-dots');
            if (!dotsContainer) {
                dotsContainer = document.createElement('div');
                dotsContainer.className = 'testimonial-dots';
                homeTestimonialContainer.appendChild(dotsContainer);
                
                // Tạo dots cho mỗi testimonial
                testimonials.forEach((_, index) => {
                    const dot = document.createElement('span');
                    dot.className = 'dot' + (index === 0 ? ' active' : '');
                    dot.setAttribute('data-index', index);
                    dotsContainer.appendChild(dot);
                });
            }
            
            // Thiết lập hiệu ứng chạy tự động
            let currentIndex = 0;
            testimonials.forEach((testimonial, index) => {
                testimonial.style.display = index === 0 ? 'block' : 'none';
            });
            
            // Chuyển đổi giữa các testimonial
            function showTestimonial(index) {
                testimonials.forEach((testimonial, i) => {
                    testimonial.style.display = i === index ? 'block' : 'none';
                    
                    // Thêm hiệu ứng fade cho smooth transition
                    if (i === index) {
                        testimonial.style.opacity = 0;
                        setTimeout(() => {
                            testimonial.style.opacity = 1;
                        }, 50);
                    }
                });
                
                // Cập nhật active dot
                const dots = dotsContainer.querySelectorAll('.dot');
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === index);
                });
                
                currentIndex = index;
            }
            
            // Auto rotate testimonials mỗi 10 giây
            const interval = setInterval(() => {
                const nextIndex = (currentIndex + 1) % testimonials.length;
                showTestimonial(nextIndex);
            }, 10000); // Đã thay đổi từ 5000 (5 giây) thành 10000 (10 giây)
            
            // Xử lý khi click vào dots
            const dots = dotsContainer.querySelectorAll('.dot');
            dots.forEach(dot => {
                dot.addEventListener('click', () => {
                    const index = parseInt(dot.getAttribute('data-index'));
                    showTestimonial(index);
                    clearInterval(interval); // Dừng auto-rotate khi người dùng tương tác
                });
            });
        }
    }
    
    // Xử lý slider cho phần testimonial ở trang testimonials
    const testimonialSlider = document.querySelector('.testimonials-container');
    if (testimonialSlider) {
        const testimonials = testimonialSlider.querySelectorAll('.testimonial');
        const dots = document.querySelectorAll('.testimonial-dots .dot');
        
        if (testimonials.length > 0 && dots.length > 0) {
            let currentSlideIndex = 0;
            
            function showSlide(n) {
                testimonials.forEach(testimonial => {
                    testimonial.classList.remove('active');
                });
                
                dots.forEach(dot => {
                    dot.classList.remove('active');
                });
                
                testimonials[n].classList.add('active');
                dots[n].classList.add('active');
                currentSlideIndex = n;
            }
            
            function nextSlide() {
                currentSlideIndex = (currentSlideIndex + 1) % testimonials.length;
                showSlide(currentSlideIndex);
            }
            
            // Auto slide every 10 seconds
            const sliderInterval = setInterval(nextSlide, 10000); // Đã thay đổi từ 5000 (5 giây) thành 10000 (10 giây)
            
            // Dot click event
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showSlide(index);
                    clearInterval(sliderInterval); // Dừng auto-rotate khi người dùng tương tác
                });
            });
            
            // Make currentSlide global
            window.currentSlide = function(n) {
                showSlide(n);
                clearInterval(sliderInterval);
            };
        }
    }
}

// Initialize specific page features
function initCurrentPage() {
    const currentPath = window.location.pathname;
    
    if (currentPath.includes('index.php') || currentPath.endsWith('/')) {
        // Home page specific initializations
    }
    else if (currentPath.includes('services.php')) {
        // Services page specific initializations
    }
    else if (currentPath.includes('about.php')) {
        // About page specific initializations
    }
    else if (currentPath.includes('testimonials.php')) {
        // Testimonials page specific initializations
    }
    else if (currentPath.includes('contact.php')) {
        // Contact page specific initializations
    }
}