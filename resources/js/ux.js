// Smooth scroll for in-page anchors
(() => {
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href^="#"]');
    if (!a) return;
    const id = a.getAttribute('href').slice(1);
    const el = document.getElementById(id);
    if (el) {
      e.preventDefault();
      el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
})();

// Scroll progress bar (top thin line)
(() => {
  const bar = document.createElement('div');
  bar.style.position = 'fixed';
  bar.style.top = '0';
  bar.style.left = '0';
  bar.style.height = '3px';
  bar.style.width = '0%';
  bar.style.background = 'linear-gradient(90deg,#6366f1,#ec4899)';
  bar.style.zIndex = '60';
  document.addEventListener('DOMContentLoaded', () => document.body.appendChild(bar));

  const setProgress = () => {
    const st = window.scrollY || document.documentElement.scrollTop;
    const dh = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    bar.style.width = (dh > 0 ? (st / dh) * 100 : 0) + '%';
  };
  window.addEventListener('scroll', setProgress, { passive: true });
  window.addEventListener('resize', setProgress);
  setProgress();
})();

// Intersection-based reveal (AOS-lite)
(() => {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) entry.target.classList.add('in-view');
    });
  }, { threshold: 0.12 });
  const init = () => {
    document.querySelectorAll('[data-animate], .underline-sweep, .ls-expand').forEach((el) => observer.observe(el));
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();

// Sticky navbar slide behavior
(() => {
  let lastY = window.scrollY;
  const nav = document.querySelector('[data-nav]');
  if (!nav) return;
  window.addEventListener('scroll', () => {
    const y = window.scrollY;
    if (y > lastY && y > 64) { nav.classList.remove('nav-visible'); nav.classList.add('nav-hidden'); }
    else { nav.classList.remove('nav-hidden'); nav.classList.add('nav-visible'); }
    lastY = y;
  }, { passive: true });
})();

// Typewriter, word-fade, letter-pop utilities
export function typewriter(el, text, speed = 35) {
  if (!el) return;
  el.innerHTML = '';
  const span = document.createElement('span');
  el.appendChild(span);
  let i = 0;
  const tick = () => {
    span.textContent = text.slice(0, i++);
    if (i <= text.length) requestAnimationFrame(tick);
  };
  requestAnimationFrame(tick);
}

export function wordFade(el, delay = 120) {
  if (!el) return;
  const words = el.textContent.trim().split(/\s+/);
  el.innerHTML = words.map(w => `<span class="word">${w}</span>`).join(' ');
  const items = el.querySelectorAll('.word');
  items.forEach((w, idx) => setTimeout(() => w.classList.add('in'), idx * delay));
}

export function letterPop(el, delay = 20) {
  if (!el) return;
  const text = el.textContent;
  el.classList.add('letter-pop');
  el.innerHTML = [...text].map(ch => `<span>${ch}</span>`).join('');
  el.querySelectorAll('span').forEach((s, idx) => setTimeout(() => s.classList.add('in'), idx * delay));
}

// Auto-initialize typewriter/word-fade/letter-pop via data-attributes
(() => {
  const init = () => {
    document.querySelectorAll('[data-typewriter]')
      .forEach((el) => typewriter(el, el.getAttribute('data-typewriter')));
    document.querySelectorAll('[data-word-fade]')
      .forEach((el) => wordFade(el, Number(el.getAttribute('data-delay')||120)));
    document.querySelectorAll('[data-letter-pop]')
      .forEach((el) => letterPop(el, Number(el.getAttribute('data-delay')||20)));
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();

// Optional tilt effect on [data-tilt] elements (lightweight)
(() => {
  const els = new Set();
  const handle = (e) => {
    els.forEach((el) => {
      const rect = el.getBoundingClientRect();
      const cx = rect.left + rect.width / 2;
      const cy = rect.top + rect.height / 2;
      const dx = (e.clientX - cx) / rect.width;
      const dy = (e.clientY - cy) / rect.height;
      const rx = (dy * -8).toFixed(2);
      const ry = (dx * 8).toFixed(2);
      el.style.transform = `perspective(600px) rotateX(${rx}deg) rotateY(${ry}deg)`;
    });
  };
  const reset = () => els.forEach((el) => el.style.transform = '');
  const init = () => {
    document.querySelectorAll('[data-tilt]').forEach((el) => els.add(el));
    window.addEventListener('mousemove', handle);
    window.addEventListener('mouseleave', reset);
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();


