// assets/js/main.js

document.addEventListener("DOMContentLoaded", (event) => {
    
    // Initialize Lenis for Smooth Scrolling
    const lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        direction: 'vertical',
        gestureDirection: 'vertical',
        smooth: true,
        mouseMultiplier: 1,
        smoothTouch: false,
        touchMultiplier: 2,
        infinite: false,
    });

    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }

    requestAnimationFrame(raf);

    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });

    // GSAP Animations

    // Header scroll effect
    const header = document.getElementById('main-header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Hero Section Animations
    gsap.to('.hero-title', {
        y: 0,
        opacity: 1,
        duration: 1.5,
        ease: "power4.out",
        delay: 0.2
    });

    gsap.utils.toArray('.fade-up').forEach((elem, i) => {
        gsap.to(elem, {
            y: 0,
            opacity: 1,
            duration: 1,
            ease: "power3.out",
            delay: 0.5 + (i * 0.2)
        });
    });

    // Animated Counters
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        counter.innerText = '0';
        const updateCounter = () => {
            const target = +counter.getAttribute('data-target');
            const c = +counter.innerText;
            const increment = target / 200; // Adjust speed

            if (c < target) {
                counter.innerText = `${Math.ceil(c + increment)}`;
                setTimeout(updateCounter, 10);
            } else {
                // Formatting for thousands
                if(target >= 1000) {
                    counter.innerText = (target / 1000) + 'k+';
                } else {
                    counter.innerText = target + '+';
                }
            }
        };

        // ScrollTrigger for counter
        ScrollTrigger.create({
            trigger: counter,
            start: "top 80%",
            onEnter: () => updateCounter(),
            once: true
        });
    });

    // Hover Lift / 3D Tilt for cards (Vanilla JS approach)
    const cards = document.querySelectorAll('.collection-card, .product-card');
    cards.forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = ((y - centerY) / centerY) * -10;
            const rotateY = ((x - centerX) / centerX) * 10;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        });

        card.addEventListener('mouseleave', function() {
            card.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
            card.style.transition = 'transform 0.5s ease';
        });

        card.addEventListener('mouseenter', function() {
            card.style.transition = 'none';
        });

    });

});


// Global E-Commerce Functions
window.addToCart = function(productId) {
    const qtyInput = document.getElementById('qty');
    const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
    const btn = event.currentTarget || event.target;
    const originalContent = btn.innerHTML;
    const base = window.SCENTLEEN_BASE || '/';

    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    btn.disabled = true;
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('qty', qty);

    fetch(base + 'ajax_add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        if(data.success) {
            btn.innerHTML = '<i class="fas fa-check"></i> Added!';
            btn.style.backgroundColor = '#27ae60';
            btn.style.borderColor = '#27ae60';
            btn.style.color = '#fff';

            // Update cart badge in header
            const badge = document.querySelector('.cart-count-badge');
            if (badge && data.cart_count) {
                badge.innerText = data.cart_count;
                badge.style.display = 'flex';
            } else if (data.cart_count > 0) {
                // Badge might not exist yet — add it
                const cartLink = document.querySelector('.cart-icon-wrapper');
                if (cartLink && !cartLink.querySelector('.cart-count-badge')) {
                    const b = document.createElement('span');
                    b.className = 'cart-count-badge';
                    b.innerText = data.cart_count;
                    cartLink.appendChild(b);
                }
            }

            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.style.backgroundColor = '';
                btn.style.borderColor = '';
                btn.style.color = '';
            }, 2000);
        } else {
            btn.innerHTML = originalContent;
            alert(data.message || 'Could not add to cart.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalContent;
        console.error('addToCart error:', err);
    });
};

window.toggleWishlist = function(elem, productId) {
    const btn = (elem && elem instanceof Element) ? elem : (event.currentTarget || event.target);
    const base = window.SCENTLEEN_BASE || '/';
    
    const formData = new FormData();
    formData.append('product_id', productId);

    fetch(base + 'ajax_toggle_wishlist.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            if(data.is_active) {
                btn.classList.add('active');
                btn.style.color = '#e74c3c';
                if (btn.querySelector('i')) btn.querySelector('i').className = 'fas fa-heart';
            } else {
                btn.classList.remove('active');
                btn.style.color = '#ccc';
                if (btn.querySelector('i')) btn.querySelector('i').className = 'far fa-heart';
            }
        }
    })
    .catch(err => console.error('toggleWishlist error:', err));
};
