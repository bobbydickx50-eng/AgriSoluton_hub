// AgriSolutions Hub - Main JavaScript File

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initMobileMenu();
    initModals();
    initForms();
    initNavigation();
    initStatistics();
    initMarketFeatures();
    initWeatherFeatures();
    initOrderSystem();
    
    // Start data updates
    startDataUpdates();
});

// Mobile Menu Toggle
function initMobileMenu() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mainNav = document.getElementById('mainNav');
    
    if (mobileMenuBtn && mainNav) {
        mobileMenuBtn.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            mobileMenuBtn.innerHTML = mainNav.classList.contains('active') 
                ? '<i class="fas fa-times"></i>' 
                : '<i class="fas fa-bars"></i>';
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenuBtn.contains(event.target) && !mainNav.contains(event.target)) {
                mainNav.classList.remove('active');
                mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });
    }
}

// Modal Functions
function initModals() {
    // Login Modal
    const loginBtn = document.getElementById('loginBtn');
    const signupBtn = document.getElementById('signupBtn');
    const loginModal = document.getElementById('loginModal');
    const signupModal = document.getElementById('signupModal');
    const closeModals = document.querySelectorAll('.close-modal');
    const switchToSignup = document.getElementById('switchToSignup');
    const switchToLogin = document.getElementById('switchToLogin');
    
    // Open Login Modal
    if (loginBtn && loginModal) {
        loginBtn.addEventListener('click', function(e) {
            e.preventDefault();
            loginModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Open Signup Modal
    if (signupBtn && signupModal) {
        signupBtn.addEventListener('click', function(e) {
            e.preventDefault();
            signupModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close Modals
    closeModals.forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            if (loginModal) loginModal.style.display = 'none';
            if (signupModal) signupModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === loginModal) {
            loginModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        if (e.target === signupModal) {
            signupModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
    
    // Switch between login and signup
    if (switchToSignup) {
        switchToSignup.addEventListener('click', function(e) {
            e.preventDefault();
            if (loginModal) loginModal.style.display = 'none';
            if (signupModal) signupModal.style.display = 'flex';
        });
    }
    
    if (switchToLogin) {
        switchToLogin.addEventListener('click', function(e) {
            e.preventDefault();
            if (signupModal) signupModal.style.display = 'none';
            if (loginModal) loginModal.style.display = 'flex';
        });
    }
}

// Form Handling
function initForms() {
    // Login Form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            
            // Simple validation
            if (!email || !password) {
                showNotification('Please fill in all fields', 'error');
                return;
            }
            
            // Simulate API call
            showNotification('Logging in...', 'info');
            
            setTimeout(() => {
                showNotification('Login successful! Welcome back.', 'success');
                loginForm.reset();
                document.getElementById('loginModal').style.display = 'none';
                document.body.style.overflow = 'auto';
                
                // Update UI for logged in user
                updateUserStatus(email);
            }, 1500);
        });
    }
    
    // Signup Form
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('signupName').value;
            const email = document.getElementById('signupEmail').value;
            const phone = document.getElementById('signupPhone').value;
            const country = document.getElementById('signupCountry').value;
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('signupConfirm').value;
            
            // Validation
            if (!name || !email || !phone || !country || !password || !confirmPassword) {
                showNotification('Please fill in all fields', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                showNotification('Passwords do not match', 'error');
                return;
            }
            
            if (password.length < 6) {
                showNotification('Password must be at least 6 characters', 'error');
                return;
            }
            
            // Simulate API call
            showNotification('Creating your account...', 'info');
            
            setTimeout(() => {
                showNotification('Account created successfully! Welcome to AgriSolutions Hub.', 'success');
                signupForm.reset();
                document.getElementById('signupModal').style.display = 'none';
                document.body.style.overflow = 'auto';
                
                // Update UI
                updateUserStatus(email);
            }, 2000);
        });
    }
    
    // Contact Form
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('contactName').value;
            const email = document.getElementById('contactEmail').value;
            const subject = document.getElementById('contactSubject').value;
            const message = document.getElementById('contactMessage').value;
            
            if (!name || !email || !subject || !message) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }
            
            showNotification('Sending your message...', 'info');
            
            setTimeout(() => {
                showNotification('Message sent successfully! We will respond within 24 hours.', 'success');
                contactForm.reset();
                
                // Show success message
                const successMessage = document.getElementById('successMessage');
                if (successMessage) {
                    successMessage.style.display = 'block';
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 5000);
                }
            }, 1500);
        });
    }
    
    // Order Form
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get order data
            const orderData = {
                name: document.getElementById('orderName').value,
                email: document.getElementById('orderEmail').value,
                phone: document.getElementById('orderPhone').value,
                address: document.getElementById('orderAddress').value,
                city: document.getElementById('orderCity').value,
                region: document.getElementById('orderRegion').value,
                country: document.getElementById('orderCountry').value,
                payment: document.querySelector('input[name="payment"]:checked').value,
                notes: document.getElementById('orderNotes').value,
                cart: getCartItems()
            };
            
            // Validation
            if (!orderData.name || !orderData.email || !orderData.phone || !orderData.address || 
                !orderData.city || !orderData.region || !orderData.country) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }
            
            if (orderData.cart.length === 0) {
                showNotification('Your cart is empty', 'error');
                return;
            }
            
            showNotification('Processing your order...', 'info');
            
            setTimeout(() => {
                // Generate order ID
                const orderId = 'AG-' + new Date().getFullYear() + '-' + 
                    String(Math.floor(Math.random() * 10000)).padStart(4, '0');
                
                // Update confirmation display
                const orderIdDisplay = document.getElementById('orderIdDisplay');
                const orderTotalDisplay = document.getElementById('orderTotalDisplay');
                
                if (orderIdDisplay) orderIdDisplay.textContent = orderId;
                if (orderTotalDisplay) orderTotalDisplay.textContent = 
                    calculateCartTotal().toLocaleString();
                
                // Show confirmation
                document.getElementById('checkoutForm').style.display = 'none';
                document.getElementById('orderConfirmation').style.display = 'block';
                
                showNotification('Order placed successfully! Order ID: ' + orderId, 'success');
                
                // Clear cart
                clearCart();
            }, 2000);
        });
    }
}

// Navigation
function initNavigation() {
    // Update active nav link based on current page
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.main-nav a');
    
    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href');
        if (linkPage === currentPage || 
            (currentPage === '' && linkPage === 'index.html') ||
            (currentPage === 'index.html' && linkPage === 'index.html')) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href === '#') return;
            
            if (href.startsWith('#')) {
                e.preventDefault();
                const targetId = href.substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    // Close mobile menu if open
                    const mainNav = document.getElementById('mainNav');
                    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                    if (mainNav && mainNav.classList.contains('active')) {
                        mainNav.classList.remove('active');
                        if (mobileMenuBtn) {
                            mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
                        }
                    }
                    
                    // Scroll to target
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
}

// Statistics Counter Animation
function initStatistics() {
    const counters = document.querySelectorAll('.stat-number');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16);
        let current = 0;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                counter.textContent = Math.floor(current).toLocaleString();
                setTimeout(updateCounter, 16);
            } else {
                counter.textContent = target.toLocaleString();
            }
        };
        
        // Start animation when element is in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(counter);
    });
}

// Market Features
function initMarketFeatures() {
    // Market Price Filtering
    const filterBtns = document.querySelectorAll('.filter-btn');
    const marketRows = document.querySelectorAll('.market-table tbody tr');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active button
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // Filter rows
            marketRows.forEach(row => {
                if (filter === 'all' || row.getAttribute('data-category') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    
    // Refresh Prices Button
    const refreshBtn = document.getElementById('refreshPrices');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
            
            simulatePriceUpdate();
            
            setTimeout(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh Prices';
                
                // Update timestamp
                const lastUpdateTime = document.getElementById('lastUpdateTime');
                if (lastUpdateTime) {
                    const now = new Date();
                    lastUpdateTime.textContent = now.toLocaleTimeString([], { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                }
                
                showNotification('Market prices updated successfully', 'success');
            }, 1500);
        });
    }
    
    // Product Category Filtering (Order Page)
    const categoryBtns = document.querySelectorAll('.category-btn');
    const productCards = document.querySelectorAll('.product-card');
    
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active button
            categoryBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const category = this.getAttribute('data-category');
            
            // Filter products
            productCards.forEach(card => {
                if (category === 'all' || card.getAttribute('data-category') === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// Weather Features
function initWeatherFeatures() {
    // Location Selector
    const locationSelect = document.getElementById('locationSelect');
    if (locationSelect) {
        locationSelect.addEventListener('change', function() {
            const location = this.value;
            updateWeatherForLocation(location);
        });
    }
    
    // Refresh Weather Button
    const refreshWeather = document.getElementById('refreshWeather');
    if (refreshWeather) {
        refreshWeather.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
            
            setTimeout(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh';
                updateWeatherData();
                showNotification('Weather data updated', 'success');
            }, 1000);
        });
    }
    
    // Initialize weather data
    updateWeatherData();
    loadForecastData();
}

// Order System
function initOrderSystem() {
    // Product Quantity Controls
    document.querySelectorAll('.product-card').forEach(card => {
        const minusBtn = card.querySelector('.quantity-btn.minus');
        const plusBtn = card.querySelector('.quantity-btn.plus');
        const quantityInput = card.querySelector('.quantity-input');
        const addToCartBtn = card.querySelector('.btn-add-to-cart');
        
        if (minusBtn && plusBtn && quantityInput) {
            minusBtn.addEventListener('click', () => {
                let value = parseInt(quantityInput.value);
                if (value > 0) {
                    quantityInput.value = value - 1;
                }
            });
            
            plusBtn.addEventListener('click', () => {
                let value = parseInt(quantityInput.value);
                const max = parseInt(quantityInput.getAttribute('max'));
                if (value < max) {
                    quantityInput.value = value + 1;
                }
            });
            
            quantityInput.addEventListener('change', function() {
                let value = parseInt(this.value);
                const max = parseInt(this.getAttribute('max'));
                const min = parseInt(this.getAttribute('min'));
                
                if (value < min) this.value = min;
                if (value > max) this.value = max;
                if (isNaN(value)) this.value = min;
            });
        }
        
        // Add to Cart Button
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function() {
                const productCard = this.closest('.product-card');
                const productName = productCard.querySelector('h3').textContent;
                const productPrice = parseFloat(
                    productCard.querySelector('.product-price').textContent
                        .replace('TZS ', '')
                        .replace(' / kg', '')
                        .replace(' / units', '')
                        .replace(/,/g, '')
                );
                const quantity = parseInt(productCard.querySelector('.quantity-input').value);
                const unit = productCard.querySelector('.quantity-unit').textContent;
                const stock = productCard.querySelector('.product-stock').textContent;
                
                if (quantity > 0) {
                    addToCart({
                        name: productName,
                        price: productPrice,
                        quantity: quantity,
                        unit: unit,
                        stock: stock
                    });
                    
                    // Reset quantity
                    productCard.querySelector('.quantity-input').value = 0;
                    
                    showNotification(`${quantity} ${unit} of ${productName} added to cart`, 'success');
                } else {
                    showNotification('Please select quantity first', 'warning');
                }
            });
        }
    });
    
    // Checkout Button
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            const cartItems = getCartItems();
            if (cartItems.length === 0) {
                showNotification('Your cart is empty', 'warning');
                return;
            }
            
            // Hide product selection, show checkout form
            document.querySelector('.product-selection').style.display = 'none';
            document.querySelector('.order-summary').style.display = 'none';
            document.getElementById('checkoutForm').style.display = 'block';
            
            // Update process steps
            updateProcessSteps(2);
        });
    }
    
    // Back to Cart Button
    const backToCart = document.getElementById('backToCart');
    if (backToCart) {
        backToCart.addEventListener('click', function() {
            document.querySelector('.product-selection').style.display = 'block';
            document.querySelector('.order-summary').style.display = 'block';
            document.getElementById('checkoutForm').style.display = 'none';
            
            updateProcessSteps(1);
        });
    }
    
    // Clear Cart Button
    const clearCartBtn = document.getElementById('clearCart');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to clear your cart?')) {
                clearCart();
                showNotification('Cart cleared', 'success');
            }
        });
    }
    
    // Print Invoice Button
    const printInvoiceBtn = document.getElementById('printInvoice');
    if (printInvoiceBtn) {
        printInvoiceBtn.addEventListener('click', function() {
            window.print();
        });
    }
    
    // Track Order Button
    const trackOrderBtn = document.getElementById('trackOrder');
    if (trackOrderBtn) {
        trackOrderBtn.addEventListener('click', function() {
            showNotification('Order tracking feature coming soon!', 'info');
        });
    }
    
    // Initialize cart
    updateCartDisplay();
}

// Cart Functions
let cart = [];

function addToCart(product) {
    // Check if product already in cart
    const existingIndex = cart.findIndex(item => item.name === product.name);
    
    if (existingIndex > -1) {
        cart[existingIndex].quantity += product.quantity;
    } else {
        cart.push({
            id: Date.now(),
            name: product.name,
            price: product.price,
            quantity: product.quantity,
            unit: product.unit
        });
    }
    
    updateCartDisplay();
    saveCartToLocalStorage();
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCartDisplay();
    saveCartToLocalStorage();
}

function updateCartItemQuantity(productId, newQuantity) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity = newQuantity;
        if (item.quantity <= 0) {
            removeFromCart(productId);
        } else {
            updateCartDisplay();
            saveCartToLocalStorage();
        }
    }
}

function getCartItems() {
    return cart;
}

function calculateCartTotal() {
    return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
}

function clearCart() {
    cart = [];
    updateCartDisplay();
    saveCartToLocalStorage();
}

function updateCartDisplay() {
    const cartItemsContainer = document.getElementById('cartItems');
    const cartEmpty = document.querySelector('.cart-empty');
    const subtotalElement = document.querySelector('.summary-row:nth-child(1) .summary-value');
    const taxElement = document.querySelector('.summary-row:nth-child(3) .summary-value');
    const totalElement = document.querySelector('.summary-row.total .summary-value');
    
    if (!cartItemsContainer) return;
    
    // Clear container
    cartItemsContainer.innerHTML = '';
    
    if (cart.length === 0) {
        // Show empty cart message
        if (cartEmpty) {
            cartItemsContainer.appendChild(cartEmpty.cloneNode(true));
        }
    } else {
        // Add cart items
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.innerHTML = `
                <div class="cart-item-info">
                    <h4>${item.name}</h4>
                    <p>TZS ${item.price.toLocaleString()} / ${item.unit}</p>
                </div>
                <div class="cart-item-actions">
                    <div class="cart-item-quantity">
                        <button class="minus" data-id="${item.id}">-</button>
                        <span>${item.quantity}</span>
                        <button class="plus" data-id="${item.id}">+</button>
                    </div>
                    <div class="cart-item-total">
                        TZS ${itemTotal.toLocaleString()}
                    </div>
                    <button class="cart-item-remove" data-id="${item.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            
            cartItemsContainer.appendChild(cartItem);
        });
        
        // Add event listeners to new buttons
        cartItemsContainer.querySelectorAll('.minus').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = parseInt(this.getAttribute('data-id'));
                const item = cart.find(item => item.id === productId);
                if (item) {
                    updateCartItemQuantity(productId, item.quantity - 1);
                }
            });
        });
        
        cartItemsContainer.querySelectorAll('.plus').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = parseInt(this.getAttribute('data-id'));
                const item = cart.find(item => item.id === productId);
                if (item) {
                    updateCartItemQuantity(productId, item.quantity + 1);
                }
            });
        });
        
        cartItemsContainer.querySelectorAll('.cart-item-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = parseInt(this.getAttribute('data-id'));
                removeFromCart(productId);
            });
        });
    }
    
    // Update totals
    const subtotal = calculateCartTotal();
    const tax = subtotal * 0.18; // 18% VAT
    const shipping = 15000;
    const total = subtotal + tax + shipping;
    
    if (subtotalElement) subtotalElement.textContent = 'TZS ' + subtotal.toLocaleString();
    if (taxElement) taxElement.textContent = 'TZS ' + tax.toLocaleString();
    if (totalElement) totalElement.textContent = 'TZS ' + total.toLocaleString();
}

function saveCartToLocalStorage() {
    try {
        localStorage.setItem('agrisolutions_cart', JSON.stringify(cart));
    } catch (e) {
        console.error('Error saving cart to localStorage:', e);
    }
}

function loadCartFromLocalStorage() {
    try {
        const savedCart = localStorage.getItem('agrisolutions_cart');
        if (savedCart) {
            cart = JSON.parse(savedCart);
            updateCartDisplay();
        }
    } catch (e) {
        console.error('Error loading cart from localStorage:', e);
        cart = [];
    }
}

// Process Steps
function updateProcessSteps(stepNumber) {
    const steps = document.querySelectorAll('.step');
    steps.forEach((step, index) => {
        if (index + 1 <= stepNumber) {
            step.classList.add('active');
        } else {
            step.classList.remove('active');
        }
    });
}

// Weather Data Functions
function updateWeatherData() {
    // Update current weather
    const tempValue = document.querySelector('.temp-value');
    const conditionIcon = document.querySelector('.condition-icon');
    const conditionText = document.querySelector('.condition-text');
    
    if (tempValue && conditionIcon && conditionText) {
        // Simulate weather changes
        const currentTemp = parseInt(tempValue.textContent);
        const variation = Math.random() * 2 - 1; // -1 to +1
        const newTemp = Math.round(currentTemp + variation);
        
        tempValue.textContent = newTemp + '°C';
        
        // Update condition based on temperature
        if (newTemp > 30) {
            conditionIcon.textContent = '☀️';
            conditionText.textContent = 'Sunny';
        } else if (newTemp > 25) {
            conditionIcon.textContent = '⛅';
            conditionText.textContent = 'Partly Cloudy';
        } else {
            conditionIcon.textContent = '☁️';
            conditionText.textContent = 'Cloudy';
        }
    }
    
    // Update forecast
    updateForecastData();
}

function updateForecastData() {
    const forecastGrid = document.getElementById('forecastGrid');
    if (!forecastGrid) return;
    
    const days = ['Today', 'Tomorrow', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    const conditions = ['☀️', '⛅', '☁️', '🌧️', '⛈️'];
    const conditionTexts = ['Sunny', 'Partly Cloudy', 'Cloudy', 'Light Rain', 'Thunderstorms'];
    
    forecastGrid.innerHTML = '';
    
    days.forEach((day, index) => {
        const baseTemp = 28 + (Math.random() * 4 - 2); // 26-30
        const conditionIndex = Math.floor(Math.random() * conditions.length);
        
        const forecastDay = document.createElement('div');
        forecastDay.className = 'forecast-day';
        forecastDay.innerHTML = `
            <h3>${day}</h3>
            <div class="weather-icon">${conditions[conditionIndex]}</div>
            <div class="temp">${Math.round(baseTemp)}°C / ${Math.round(baseTemp - 9)}°C</div>
            <div class="condition">${conditionTexts[conditionIndex]}</div>
        `;
        
        forecastGrid.appendChild(forecastDay);
    });
}

function loadForecastData() {
    // This would typically load data from an API
    updateForecastData();
}

function updateWeatherForLocation(location) {
    // Simulate different weather for different locations
    const locations = {
        'morogoro': { temp: 28, condition: '⛅', text: 'Partly Cloudy' },
        'dar': { temp: 32, condition: '☀️', text: 'Sunny' },
        'arusha': { temp: 22, condition: '☁️', text: 'Cloudy' },
        'mbeya': { temp: 20, condition: '🌧️', text: 'Light Rain' },
        'dodoma': { temp: 30, condition: '☀️', text: 'Sunny' }
    };
    
    const weather = locations[location] || locations.morogoro;
    
    const tempValue = document.querySelector('.temp-value');
    const conditionIcon = document.querySelector('.condition-icon');
    const conditionText = document.querySelector('.condition-text');
    
    if (tempValue && conditionIcon && conditionText) {
        tempValue.textContent = weather.temp + '°C';
        conditionIcon.textContent = weather.condition;
        conditionText.textContent = weather.text;
    }
    
    showNotification(`Weather updated for ${location}`, 'info');
}

// Market Data Functions
function simulatePriceUpdate() {
    const priceCells = document.querySelectorAll('.market-table td:nth-child(2)');
    const changeCells = document.querySelectorAll('.market-table td:nth-child(3)');
    const trendCells = document.querySelectorAll('.market-table td:nth-child(5) div');
    
    priceCells.forEach((cell, index) => {
        const currentPrice = parseFloat(cell.textContent.replace(/,/g, ''));
        const changePercent = (Math.random() * 6 - 3); // -3% to +3%
        const newPrice = Math.round(currentPrice * (1 + changePercent / 100));
        
        cell.textContent = newPrice.toLocaleString();
        
        if (changeCells[index]) {
            const changeClass = changePercent >= 0 ? 'price-up' : 'price-down';
            const changeSymbol = changePercent >= 0 ? '+' : '';
            changeCells[index].textContent = `${changeSymbol}${changePercent.toFixed(1)}%`;
            changeCells[index].className = changeClass;
            
            // Update trend indicator
            if (trendCells[index]) {
                if (changePercent > 1) {
                    trendCells[index].className = 'trend-up';
                    trendCells[index].textContent = '↗';
                } else if (changePercent < -1) {
                    trendCells[index].className = 'trend-down';
                    trendCells[index].textContent = '↘';
                } else {
                    trendCells[index].className = 'trend-stable';
                    trendCells[index].textContent = '→';
                }
            }
        }
    });
}

// Notification System
function showNotification(message, type = 'info') {
    // Remove existing notification
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close"><i class="fas fa-times"></i></button>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${getNotificationColor(type)};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        z-index: 3000;
        animation: slideInRight 0.3s ease;
        max-width: 400px;
    `;
    
    document.body.appendChild(notification);
    
    // Add close button event
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
    
    // Add CSS for animation if not already present
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
}

function getNotificationIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function getNotificationColor(type) {
    const colors = {
        'success': '#28a745',
        'error': '#dc3545',
        'warning': '#ffc107',
        'info': '#17a2b8'
    };
    return colors[type] || '#17a2b8';
}

// User Status
function updateUserStatus(email) {
    const userActions = document.querySelector('.user-actions');
    if (userActions) {
        userActions.innerHTML = `
            <div class="user-profile">
                <i class="fas fa-user"></i>
                <span>${email.split('@')[0]}</span>
            </div>
            <button class="btn-logout" id="logoutBtn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        `;
        
        // Add logout functionality
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function() {
                // Reset UI
                userActions.innerHTML = `
                    <button class="btn-login" id="loginBtn"><i class="fas fa-sign-in-alt"></i> Login</button>
                    <button class="btn-signup" id="signupBtn"><i class="fas fa-user-plus"></i> Sign Up</button>
                `;
                
                // Re-initialize modals
                initModals();
                
                showNotification('Logged out successfully', 'info');
            });
        }
    }
}

// Data Updates
function startDataUpdates() {
    // Update market prices every 30 seconds
    setInterval(simulatePriceUpdate, 30000);
    
    // Update weather every minute
    setInterval(updateWeatherData, 60000);
    
    // Load cart from localStorage
    loadCartFromLocalStorage();
}

// Initialize when page loads
window.addEventListener('load', function() {
    // Add loading animation
    document.body.classList.add('loaded');
    
    // Check for success messages in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success')) {
        showNotification('Operation completed successfully!', 'success');
    }
    
    // Initialize any additional features
    if (typeof initPageSpecificFeatures === 'function') {
        initPageSpecificFeatures();
    }
});

// Export functions for use in console if needed
window.AgriSolutionsHub = {
    showNotification,
    updateWeatherData,
    simulatePriceUpdate,
    clearCart,
    getCartItems: () => cart,
    addToCart,
    removeFromCart
};