// Scroll splash/fruit with parallax
window.addEventListener('scroll', function(){
  const splashes = document.querySelectorAll('.juice-splash, .fruit, .fruit-leaf');
  splashes.forEach((el, i) => {
    el.style.transform = `translateY(${window.scrollY*0.08*(i+1)}px)`;
  });
});

// Scroll-to grid
function scrollToGrid() {
  document.getElementById('products').scrollIntoView({ behavior: 'smooth' });
}

// Add to Cart Toast
document.querySelectorAll('.add-cart-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    showToast('Added to cart!');
  });
});

function showToast(msg) {
  const toast = document.getElementById('toast');
  toast.textContent = msg;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 1900);
}

// Newsletter Signup
document.querySelector('.newsletter-form').addEventListener('submit', function(e){
  e.preventDefault();
  const input = this.querySelector('input[type=email]');
  const msgEl = document.getElementById('newsletter-msg');
  if(input.value.match(/^[^@\s]+@[^@\s]+\.[^@\s]+$/)) {
    msgEl.textContent = 'Thank you for subscribing!';
    msgEl.style.color = '#44c788';
    input.value = '';
  } else {
    msgEl.textContent = 'Please enter a valid email address.';
    msgEl.style.color = '#fd3573';
  }
  setTimeout(()=>{ msgEl.textContent=''; }, 2500);
});

// Testimonials Slider
const testimonialCards = document.querySelectorAll('.testimonial-card');
let currentTestimonial = 0;
function showTestimonial(idx){
  testimonialCards.forEach((c,i)=>c.classList.toggle('active', i === idx));
}
document.querySelector('.slider-btn.next').onclick = () => {
  currentTestimonial = (currentTestimonial+1)%testimonialCards.length;
  showTestimonial(currentTestimonial);
};
document.querySelector('.slider-btn.prev').onclick = () => {
  currentTestimonial = (currentTestimonial-1+testimonialCards.length)%testimonialCards.length;
  showTestimonial(currentTestimonial);
};
// Initialize
showTestimonial(currentTestimonial);
