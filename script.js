document.addEventListener('DOMContentLoaded', function() {
  initParallaxOrbs();
  initFormToggles();
  initTableRows();
  initButtonRipple();
  initParticles();
  initFadeInElements();
  initTooltips();
});

function initParallaxOrbs() {
  const orbs = document.querySelectorAll('.floating-bg .orb');
  document.addEventListener('mousemove', function(e) {
    const x = (e.clientX / window.innerWidth - 0.5) * 20;
    const y = (e.clientY / window.innerHeight - 0.5) * 20;
    orbs.forEach((orb, index) => {
      const speed = 1 + index * 0.3;
      orb.style.transform = `translate(${x * speed}px, ${y * speed}px) scale(1)`;
    });
  });
}

function initFormToggles() {
  const showBtn = document.getElementById('showFormBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const form = document.getElementById('addForm');

  if (showBtn && form) {
    showBtn.addEventListener('click', function() {
      form.classList.add('active');
      form.style.display = 'block';
      form.scrollIntoView({ behavior: 'smooth', block: 'center' });
      this.style.display = 'none';
    });
  }

  if (cancelBtn && form) {
    cancelBtn.addEventListener('click', function() {
      form.classList.remove('active');
      setTimeout(() => { form.style.display = 'none'; }, 300);
      if (showBtn) showBtn.style.display = 'inline-flex';
      const formElement = form.querySelector('form');
      if (formElement) formElement.reset();
    });
  }
}

function initTableRows() {
  document.querySelectorAll('.data-table tbody tr').forEach((row, index) => {
    row.style.opacity = '0';
    row.style.transform = 'translateX(-20px)';
    setTimeout(() => {
      row.style.transition = 'all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
      row.style.opacity = '1';
      row.style.transform = 'translateX(0)';
    }, 100 + index * 50);
  });
}

function initButtonRipple() {
  document.querySelectorAll('.btn, .action-card, .btn-add, .btn-cancel').forEach(btn => {
    btn.addEventListener('click', function(e) {
      const ripple = document.createElement('span');
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size / 2;
      const y = e.clientY - rect.top - size / 2;
      ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        pointer-events: none;
        transform: scale(0);
        animation: rippleAnim 0.6s ease-out forwards;
      `;
      this.style.position = 'relative';
      this.style.overflow = 'hidden';
      this.appendChild(ripple);
      setTimeout(() => ripple.remove(), 600);
    });
  });

  const style = document.createElement('style');
  style.textContent = `
    @keyframes rippleAnim {
      to { transform: scale(3); opacity: 0; }
    }
  `;
  document.head.appendChild(style);
}

function initParticles() {
  const container = document.querySelector('.floating-bg');
  if (!container) return;

  for (let i = 0; i < 30; i++) {
    const particle = document.createElement('div');
    const size = 2 + Math.random() * 4;
    const delay = Math.random() * 20;
    const duration = 15 + Math.random() * 25;
    particle.style.cssText = `
      position: absolute;
      width: ${size}px;
      height: ${size}px;
      background: rgba(255,255,255,0.15);
      border-radius: 50%;
      left: ${Math.random() * 100}%;
      top: ${Math.random() * 100}%;
      animation: floatParticle ${duration}s ease-in-out ${delay}s infinite;
      pointer-events: none;
    `;
    container.appendChild(particle);
  }

  const style = document.createElement('style');
  style.textContent = `
    @keyframes floatParticle {
      0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.15; }
      25% { transform: translate(${20 + Math.random() * 40}px, ${-30 + Math.random() * 60}px) scale(1.5); opacity: 0.6; }
      50% { transform: translate(${-20 + Math.random() * 40}px, ${30 + Math.random() * 60}px) scale(0.8); opacity: 0.2; }
      75% { transform: translate(${30 + Math.random() * 40}px, ${-10 + Math.random() * 40}px) scale(1.3); opacity: 0.5; }
    }
  `;
  document.head.appendChild(style);
}

function initFadeInElements() {
  const elements = document.querySelectorAll('.alert, .success-message, .error-message, .task-section');
  elements.forEach((el, index) => {
    el.style.opacity = '0';
    el.style.transform = 'scale(0.95)';
    setTimeout(() => {
      el.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
      el.style.opacity = '1';
      el.style.transform = 'scale(1)';
    }, 300 + index * 100);
  });
}

function initTooltips() {
  document.querySelectorAll('[data-tooltip]').forEach(el => {
    const tip = document.createElement('div');
    tip.textContent = el.getAttribute('data-tooltip');
    tip.style.cssText = `
      position: absolute;
      bottom: calc(100% + 8px);
      left: 50%;
      transform: translateX(-50%) scale(0.9);
      background: rgba(0,0,0,0.9);
      backdrop-filter: blur(10px);
      color: #fff;
      padding: 4px 12px;
      border-radius: 8px;
      font-size: 0.8rem;
      white-space: nowrap;
      opacity: 0;
      pointer-events: none;
      transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
      z-index: 100;
    `;
    el.style.position = 'relative';
    el.appendChild(tip);
    el.addEventListener('mouseenter', () => {
      tip.style.opacity = '1';
      tip.style.transform = 'translateX(-50%) scale(1)';
    });
    el.addEventListener('mouseleave', () => {
      tip.style.opacity = '0';
      tip.style.transform = 'translateX(-50%) scale(0.9)';
    });
  });
}

window.addEventListener('load', function() {
  document.querySelectorAll('.content-section').forEach((section, index) => {
    section.style.animationDelay = `${0.2 + index * 0.1}s`;
  });
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});