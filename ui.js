// Efek tampilan TaskMate: reveal saat scroll, animasi per kata, dan parallax ringan.
(() => {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Pecah judul .split jadi <span> per kata dengan delay bertahap.
    document.querySelectorAll('.split').forEach(el => {
        if (reduce) return;
        let i = 0;
        const wrap = node => {
            [...node.childNodes].forEach(child => {
                if (child.nodeType === Node.TEXT_NODE) {
                    const frag = document.createDocumentFragment();
                    child.textContent.split(/(\s+)/).forEach(part => {
                        if (!part) return;
                        if (/^\s+$/.test(part)) { frag.append(part); return; }
                        const span = document.createElement('span');
                        span.className = 'w-anim';
                        span.style.animationDelay = (i++ * 70) + 'ms';
                        span.textContent = part;
                        frag.append(span);
                    });
                    child.replaceWith(frag);
                } else if (child.nodeType === Node.ELEMENT_NODE) {
                    wrap(child);
                }
            });
        };
        wrap(el);
    });

    // Munculkan elemen .reveal saat masuk layar.
    const reveals = document.querySelectorAll('.reveal');
    if (reduce || !('IntersectionObserver' in window)) {
        reveals.forEach(el => el.classList.add('in'));
    } else {
        const io = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) { entry.target.classList.add('in'); io.unobserve(entry.target); }
            });
        }, { threshold: 0.12 });
        reveals.forEach((el, n) => { el.style.transitionDelay = Math.min(n, 6) * 60 + 'ms'; io.observe(el); });
    }

    // Parallax ringan untuk elemen .parallax.
    const parallax = document.querySelectorAll('.parallax');
    if (!reduce && parallax.length) {
        let ticking = false;
        const update = () => {
            parallax.forEach(el => {
                const r = el.getBoundingClientRect();
                el.style.translate = `0 ${((r.top - innerHeight / 2) * 0.06).toFixed(1)}px`;
            });
            ticking = false;
        };
        addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(update); } }, { passive: true });
        update();
    }
})();

// Hitung naik dari angka sekarang ke target (dipakai kartu statistik).
window.countTo = (el, target) => {
    if (!el) return;
    const from = Number(el.textContent) || 0;
    if (from === target || window.matchMedia('(prefers-reduced-motion: reduce)').matches) { el.textContent = target; return; }
    const start = performance.now(), dur = 900;
    const ease = t => 1 - Math.pow(1 - t, 3);
    const step = now => {
        const t = Math.min(1, (now - start) / dur);
        el.textContent = Math.round(from + (target - from) * ease(t));
        if (t < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
};
