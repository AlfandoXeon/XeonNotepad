<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?></title>
    <meta name="description" content="Xeon Notepad — Private, End-to-End Encrypted (AES-256-GCM) cloud notepad with Dual WYSIWYG & Markdown modes and intelligent Xeon AI generation.">
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/logo/NotepadIcon.png">
    
    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,300..600,0..1,-25..0&display=swap">
    
    <!-- AOS Animation Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/landing.css">
</head>
<body class="landing-body">

    <!-- ── Ambient Glowing Orbs Background (GSAP Animated) ────────── -->
    <div class="landing-bg" aria-hidden="true">
        <div class="glow-orb glow-top" id="orbTop"></div>
        <div class="glow-orb glow-mid" id="orbMid"></div>
        <div class="glow-orb glow-bottom" id="orbBottom"></div>
        <div class="grid-overlay"></div>
    </div>

    <!-- ── Top Navbar ─────────────────────────────────────────────── -->
    <header class="landing-nav" id="landingNav">
        <div class="landing-nav-inner">
            <a href="<?= APP_URL ?>/" class="landing-brand">
                <img src="<?= APP_URL ?>/logo/NotepadIcon.png" alt="Xeon Logo" class="landing-brand-logo">
                <span class="landing-brand-name">Xeon <b>Notepad</b></span>
            </a>

            <nav class="landing-links" aria-label="Main navigation">
                <a href="#features" class="landing-link">Features</a>
                <a href="#xeon-ai" class="landing-link">Xeon AI</a>
                <a href="#security" class="landing-link">Security</a>
                <a href="#developer" class="landing-link">Developer</a>
                <a href="#faq" class="landing-link">FAQ</a>
            </nav>

            <div class="landing-nav-actions">
                <?php if (!empty($isLoggedIn)): ?>
                    <a href="<?= APP_URL ?>/notes" class="lp-btn lp-btn-primary">
                        <span>Go to Dashboard</span>
                        <span class="material-symbols-outlined text-sm" aria-hidden="true">arrow_forward</span>
                    </a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/login" class="lp-btn lp-btn-ghost">Sign In</a>
                    <a href="<?= APP_URL ?>/register" class="lp-btn lp-btn-primary">
                        <span>Get Started Free</span>
                        <span class="material-symbols-outlined text-sm" aria-hidden="true">arrow_forward</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <!-- ── HERO SECTION ──────────────────────────────────────────── -->
        <section class="hero-section" id="hero">
            <div class="lp-container">

                <!-- Main Headline with Smooth Typewriter Effect -->
                <h1 class="hero-title" data-aos="fade-up" data-aos-duration="800">
                    <span class="hero-title-top">Your Ideas, Encrypted Forever.</span><br>
                    <span class="hero-title-typing-wrap">
                        <span class="hero-title-highlight" id="heroTypingText">Automated with Xeon AI.</span><span class="hero-typing-caret" id="heroTypingCaret" aria-hidden="true"></span>
                    </span>
                </h1>

                <!-- Subtitle -->
                <p class="hero-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                    The modern, high-speed online notepad built for absolute privacy. Features client-authenticated AES-256-GCM encryption, seamless WYSIWYG &amp; GitHub Markdown dual modes, and intelligent text automation powered by Xeon AI.
                </p>

                <!-- Actions -->
                <div class="hero-actions" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                    <a href="<?= APP_URL ?>/register" class="lp-btn lp-btn-primary lp-btn-lg">
                        <span>Start Writing for Free</span>
                        <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                    </a>
                    <a href="#features" class="lp-btn lp-btn-ghost lp-btn-lg">
                        <span class="material-symbols-outlined" aria-hidden="true">explore</span>
                        <span>Explore Features</span>
                    </a>
                </div>

                <!-- Trust Chips -->
                <div class="hero-trust-row" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                    <div class="trust-item">
                        <span class="material-symbols-outlined" aria-hidden="true">shield</span>
                        <span>Zero-Knowledge Encryption</span>
                    </div>
                    <div class="trust-item">
                        <span class="material-symbols-outlined" aria-hidden="true">bolt</span>
                        <span>Debounced Auto-Save</span>
                    </div>
                    <div class="trust-item">
                        <span class="material-symbols-outlined" aria-hidden="true">auto_awesome</span>
                        <span>Groq-Powered Xeon AI</span>
                    </div>
                    <div class="trust-item">
                        <span class="material-symbols-outlined" aria-hidden="true">devices</span>
                        <span>Fully Responsive</span>
                    </div>
                </div>

            </div>
        </section>

        <!-- ── FEATURES SECTION ──────────────────────────────────────── -->
        <section class="section-wrap" id="features">
            <div class="lp-container">
                <div class="section-head" data-aos="fade-up" data-aos-duration="700">
                    <span class="section-tag">Core Capabilities</span>
                    <h2 class="section-title">Built for Speed, Privacy &amp; Intelligence</h2>
                    <p class="section-desc">
                        Everything you need to capture, organize, and automate your writing workflow without compromising on personal privacy.
                    </p>
                </div>

                <div class="features-grid">
                    <!-- Feature 1 -->
                    <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-icon-box">
                            <span class="material-symbols-outlined" aria-hidden="true">lock</span>
                        </div>
                        <h3 class="feature-card-title">AES-256-GCM Encryption</h3>
                        <p class="feature-card-desc">
                            All note titles and bodies are encrypted using military-grade AES-256-GCM with unique cryptographic initialization vectors (IVs). Even the host owner cannot read your notes.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-icon-box">
                            <span class="material-symbols-outlined" aria-hidden="true">edit_note</span>
                        </div>
                        <h3 class="feature-card-title">Dual Editor Mastery</h3>
                        <p class="feature-card-desc">
                            Switch seamlessly between Rich Text (WYSIWYG) with intuitive toolbar formatting and GitHub Flavored Markdown (GFM) with live split-screen preview and code blocks.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-icon-box">
                            <span class="material-symbols-outlined" aria-hidden="true">auto_awesome</span>
                        </div>
                        <h3 class="feature-card-title">Xeon AI Intelligence</h3>
                        <p class="feature-card-desc">
                            Generate complete notes, meeting minutes, summaries, and action checklists in sub-seconds. Refine grammar, tone, or expand existing text with Groq-powered AI.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="feature-card" data-aos="fade-up" data-aos-delay="150">
                        <div class="feature-icon-box">
                            <span class="material-symbols-outlined" aria-hidden="true">sync</span>
                        </div>
                        <h3 class="feature-card-title">Lightning Auto-Save</h3>
                        <p class="feature-card-desc">
                            Never worry about lost notes. High-frequency debounced auto-save records every keystroke safely in the background with instant visual status feedback.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="feature-card" data-aos="fade-up" data-aos-delay="250">
                        <div class="feature-icon-box">
                            <span class="material-symbols-outlined" aria-hidden="true">history</span>
                        </div>
                        <h3 class="feature-card-title">Encrypted Change History</h3>
                        <p class="feature-card-desc">
                            Review detailed timelines of every edit made to your notes with human-readable diff descriptions, ensuring you can audit past revisions effortlessly.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="feature-card" data-aos="fade-up" data-aos-delay="350">
                        <div class="feature-icon-box">
                            <span class="material-symbols-outlined" aria-hidden="true">download</span>
                        </div>
                        <h3 class="feature-card-title">Multi-Format Export</h3>
                        <p class="feature-card-desc">
                            Download and share your documents in one click as standard Markdown (.md), clean Plain Text (.txt), or self-contained styled HTML (.html) files.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── XEON AI SPOTLIGHT ─────────────────────────────────────── -->
        <section class="section-wrap" id="xeon-ai">
            <div class="lp-container">
                <div class="ai-spotlight" data-aos="zoom-in" data-aos-duration="800">
                    <div class="ai-spotlight-left">
                        <span class="section-tag">AI-Powered Productive Flow</span>
                        <h3>Meet Xeon AI: Your Private Document Generator</h3>
                        <p style="color:var(--lp-text-muted); font-size:15px; line-height:1.65;">
                            Xeon AI understands your writing goals. Whether you are drafting lecture summaries, project milestones, or formal letters, select a curated template or describe what you need in natural language.
                        </p>

                        <div class="ai-template-tags">
                            <span class="ai-template-tag">
                                <span class="material-symbols-outlined">groups</span>
                                Meeting Notes
                            </span>
                            <span class="ai-template-tag">
                                <span class="material-symbols-outlined">school</span>
                                Study Summary
                            </span>
                            <span class="ai-template-tag">
                                <span class="material-symbols-outlined">checklist</span>
                                Action Checklist
                            </span>
                            <span class="ai-template-tag">
                                <span class="material-symbols-outlined">terminal</span>
                                Tech Documentation
                            </span>
                            <span class="ai-template-tag">
                                <span class="material-symbols-outlined">mail</span>
                                Formal Letter
                            </span>
                        </div>

                        <a href="<?= APP_URL ?>/register" class="lp-btn lp-btn-primary">
                            <span>Try Xeon AI Now</span>
                            <span class="material-symbols-outlined text-sm" aria-hidden="true">arrow_forward</span>
                        </a>
                    </div>

                    <div class="ai-spotlight-right">
                        <div class="ai-code-mockup">
                            <h4># Output Sample &middot; Groq llama-3.3-70b-versatile</h4>
                            <span style="color:#FFA742">## Objective</span><br>
                            Refactor frontend components into unified design tokens.<br><br>
                            <span style="color:#FFA742">## Key Milestones</span><br>
                            - Phase 1: Implement CSS variables for dark/light themes<br>
                            - Phase 2: Integrate Google Material Symbols vector system<br>
                            - Phase 3: Benchmark auto-save debounce latency (&lt;350ms)<br><br>
                            <span style="color:#FFA742">## Action Checklist</span><br>
                            - [ ] Verify CSRF header verification on AJAX requests<br>
                            - [ ] Audit zero-emoji compliance across templates
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── SECURITY & ARCHITECTURE ───────────────────────────────── -->
        <section class="section-wrap" id="security">
            <div class="lp-container">
                <div class="section-head" data-aos="fade-up" data-aos-duration="700">
                    <span class="section-tag">Zero Compromises</span>
                    <h2 class="section-title">True Privacy Built Into the Foundation</h2>
                    <p class="section-desc">
                        Unlike conventional online notepads that store your thoughts as plain text, Xeon Notepad treats every word as confidential intellectual property.
                    </p>
                </div>

                <div class="security-flow">
                    <div class="flow-step" data-aos="fade-right" data-aos-delay="100">
                        <div class="flow-step-num">Step 1</div>
                        <h4 class="flow-step-title">Local Input Sanitization</h4>
                        <p class="flow-step-desc">
                            All HTML inputs are stripped of malicious scripts, tags, and inline handlers before encryption processing.
                        </p>
                    </div>

                    <div class="flow-step" data-aos="fade-up" data-aos-delay="200">
                        <div class="flow-step-num">Step 2</div>
                        <h4 class="flow-step-title">AES-256-GCM Cipher</h4>
                        <p class="flow-step-desc">
                            Titles and content are encrypted with authenticated Galois/Counter Mode using a 32-byte secret key and 96-bit random IVs.
                        </p>
                    </div>

                    <div class="flow-step" data-aos="fade-left" data-aos-delay="300">
                        <div class="flow-step-num">Step 3</div>
                        <h4 class="flow-step-title">Encrypted MySQL Storage</h4>
                        <p class="flow-step-desc">
                            Only base64 ciphertexts and IVs are persisted in the database. Raw text never touches unencrypted storage.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── DEVELOPER SPOTLIGHT ───────────────────────────────────── -->
        <section class="section-wrap" id="developer">
            <div class="lp-container">
                <div class="dev-card" data-aos="fade-up" data-aos-duration="800">
                    <div class="dev-avatar-box">
                        <div class="dev-avatar-inner">
                            <img src="<?= APP_URL ?>/logo/NotepadIcon.png" alt="Developer Logo">
                        </div>
                    </div>
                    <span class="section-tag" style="margin-bottom:6px">Created &amp; Maintained By</span>
                    <h3 class="dev-name">Grateo Alfando Atmojo</h3>
                    <div class="dev-handle">@AlfandoXeon</div>
                    <p class="dev-bio">
                        Full-stack software developer focused on privacy-first web architecture, clean micro-interactions, and AI-accelerated productivity tools.
                    </p>
                    <div class="dev-links">
                        <a href="https://AlfandoXeon.freedev.app" target="_blank" rel="noopener noreferrer" class="lp-btn lp-btn-ghost">
                            <span class="material-symbols-outlined text-sm">language</span>
                            <span>AlfandoXeon.freedev.app</span>
                            <span class="material-symbols-outlined text-xs">north_east</span>
                        </a>
                        <a href="https://github.com/AlfandoXeon" target="_blank" rel="noopener noreferrer" class="lp-btn lp-btn-ghost">
                            <span class="material-symbols-outlined text-sm">code</span>
                            <span>github.com/AlfandoXeon</span>
                            <span class="material-symbols-outlined text-xs">north_east</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── FAQ ACCORDION ─────────────────────────────────────────── -->
        <section class="section-wrap" id="faq">
            <div class="lp-container">
                <div class="section-head" data-aos="fade-up" data-aos-duration="700">
                    <span class="section-tag">Frequently Asked Questions</span>
                    <h2 class="section-title">Answers to Common Questions</h2>
                    <p class="section-desc">
                        Everything you need to know about getting started, security, and using Xeon AI.
                    </p>
                </div>

                <div class="faq-list">
                    <!-- FAQ 1 -->
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                        <button type="button" class="faq-question-btn">
                            <span>Is Xeon Notepad completely free to use?</span>
                            <span class="material-symbols-outlined faq-chevron">expand_more</span>
                        </button>
                        <div class="faq-answer">
                            Yes, Xeon Notepad is 100% free to use. You can create an account, write unlimited encrypted notes, use auto-save, and export your files without subscriptions or advertisements.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="150">
                        <button type="button" class="faq-question-btn">
                            <span>How does the note encryption work?</span>
                            <span class="material-symbols-outlined faq-chevron">expand_more</span>
                        </button>
                        <div class="faq-answer">
                            Every note title and content is encrypted using AES-256-GCM authenticated encryption. Each save operation generates a unique random cryptographic initialization vector (IV), ensuring even identical text produces different ciphertexts.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                        <button type="button" class="faq-question-btn">
                            <span>What model powers Xeon AI?</span>
                            <span class="material-symbols-outlined faq-chevron">expand_more</span>
                        </button>
                        <div class="faq-answer">
                            Xeon AI runs on high-speed Llama models hosted on Groq Cloud (defaulting to llama-3.3-70b-versatile), providing sub-second generation speeds with high markdown comprehension. You can also provide your own custom Groq API key in the Preferences settings.
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="250">
                        <button type="button" class="faq-question-btn">
                            <span>Can I switch between Markdown and Rich Text anytime?</span>
                            <span class="material-symbols-outlined faq-chevron">expand_more</span>
                        </button>
                        <div class="faq-answer">
                            Yes. Xeon Notepad features dual editor support with instant bi-directional conversion using Turndown and Marked engines. You can toggle between WYSIWYG mode and GFM Markdown with split-preview at any time without losing formatting.
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                        <button type="button" class="faq-question-btn">
                            <span>Can I use Xeon Notepad on mobile devices?</span>
                            <span class="material-symbols-outlined faq-chevron">expand_more</span>
                        </button>
                        <div class="faq-answer">
                            Absolutely. The interface is 100% mobile responsive with touch-optimized scrollable toolbars, mobile navigation drawers, and clean single-column card grids.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ── FINAL CALL TO ACTION ──────────────────────────────────── -->
        <section class="section-wrap" style="padding-bottom:110px;">
            <div class="lp-container">
                <div class="bottom-cta" data-aos="zoom-in" data-aos-duration="850">
                    <span class="section-tag" style="margin-bottom:8px">Get Started Today</span>
                    <h2 class="bottom-cta-title">Ready for a Faster, Smarter &amp; Safer Notepad?</h2>
                    <p class="bottom-cta-desc">
                        Join Xeon Notepad and experience the blend of zero-knowledge privacy, dual-mode flexibility, and Xeon AI generation.
                    </p>
                    <div style="display:flex; justify-content:center; gap:14px; flex-wrap:wrap;">
                        <a href="<?= APP_URL ?>/register" class="lp-btn lp-btn-primary lp-btn-lg">
                            <span>Create Free Account</span>
                            <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                        </a>
                        <a href="<?= APP_URL ?>/login" class="lp-btn lp-btn-ghost lp-btn-lg">
                            <span>Sign In</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ── FOOTER ────────────────────────────────────────────────── -->
    <footer class="landing-footer">
        <div class="lp-container">
            <div class="footer-inner">
                <div style="display:flex; align-items:center; gap:10px;">
                    <img src="<?= APP_URL ?>/logo/NotepadIcon.png" alt="" width="24" height="24">
                    <span style="font-weight:700; color:#FFFFFF;">Xeon <b>Notepad</b></span>
                    <span style="color:var(--lp-text-subtle);">&middot; Developed by AlfandoXeon</span>
                </div>
                <div class="footer-links">
                    <a href="#features">Features</a>
                    <a href="#xeon-ai">Xeon AI</a>
                    <a href="#security">Security</a>
                    <a href="https://AlfandoXeon.freedev.app" target="_blank" rel="noopener noreferrer">Author</a>
                    <a href="<?= APP_URL ?>/login">Sign In</a>
                    <a href="<?= APP_URL ?>/register">Register</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- GSAP & AOS Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <!-- Interactive Scripts for Landing Page -->
    <script>
    // Initialize AOS
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 700,
                once: true,
                offset: 60,
                easing: 'ease-out-cubic'
            });
        }

        // GSAP Entrance & Floating Ambient Orbs
        if (typeof gsap !== 'undefined') {
            // Smooth page load entrance
            gsap.from('#landingNav', {
                y: -25,
                opacity: 0,
                duration: 0.8,
                ease: 'power3.out'
            });
            gsap.from('.hero-title-top', {
                y: 30,
                opacity: 0,
                duration: 0.9,
                delay: 0.15,
                ease: 'power3.out'
            });
            gsap.from('.hero-title-typing-wrap', {
                y: 25,
                opacity: 0,
                duration: 0.9,
                delay: 0.3,
                ease: 'power3.out'
            });
            gsap.from('.hero-subtitle', {
                y: 20,
                opacity: 0,
                duration: 0.8,
                delay: 0.45,
                ease: 'power3.out'
            });
            gsap.from('.hero-actions', {
                y: 20,
                opacity: 0,
                duration: 0.8,
                delay: 0.6,
                ease: 'power3.out'
            });
            gsap.from('.hero-trust-row .trust-item', {
                y: 15,
                opacity: 0,
                duration: 0.6,
                stagger: 0.08,
                delay: 0.75,
                ease: 'power2.out'
            });

            // Ambient Orbs Floating Loop
            gsap.to('#orbTop', {
                x: '+=60',
                y: '+=40',
                duration: 6,
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut'
            });
            gsap.to('#orbMid', {
                x: '-=50',
                y: '+=70',
                duration: 8,
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut'
            });
            gsap.to('#orbBottom', {
                x: '+=45',
                y: '-=60',
                duration: 7,
                repeat: -1,
                yoyo: true,
                ease: 'sine.inOut'
            });
        }

        // Dynamic Hero Typing Effect
        var typingEl = document.getElementById('heroTypingText');
        var phrases = [
            'Automated with Xeon AI.',
            'Zero-Knowledge AES-256-GCM.',
            'Dual WYSIWYG & GFM Modes.',
            'Sub-Second Debounced Auto-Save.',
            'Structured Note Generation in Seconds.'
        ];
        var phraseIndex = 0;
        var charIndex = phrases[0].length;
        var isDeleting = true;
        var typeSpeed = 50;

        function runTypingLoop() {
            var currentPhrase = phrases[phraseIndex];

            if (isDeleting) {
                charIndex--;
                if (typingEl) typingEl.textContent = currentPhrase.substring(0, charIndex);
                if (charIndex === 0) {
                    isDeleting = false;
                    phraseIndex = (phraseIndex + 1) % phrases.length;
                    setTimeout(runTypingLoop, 350);
                    return;
                }
                setTimeout(runTypingLoop, 28);
            } else {
                charIndex++;
                if (typingEl) typingEl.textContent = currentPhrase.substring(0, charIndex);
                if (charIndex === currentPhrase.length) {
                    isDeleting = true;
                    setTimeout(runTypingLoop, 2200); // pause at full phrase
                    return;
                }
                setTimeout(runTypingLoop, 45);
            }
        }

        // Start typing loop after initial display
        setTimeout(runTypingLoop, 2400);

        // FAQ Accordion
        document.querySelectorAll('.faq-question-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var item = btn.closest('.faq-item');
                var isActive = item.classList.contains('active');
                document.querySelectorAll('.faq-item').forEach(function(i) {
                    i.classList.remove('active');
                });
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    });
    </script>
</body>
</html>
