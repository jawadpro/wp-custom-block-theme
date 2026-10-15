#!/usr/bin/env python3
"""Generates /patterns/*.php from one source so block markup stays consistent.
Run: python3 tools/build-patterns.py"""
import os, json
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT = os.path.join(ROOT, 'patterns')
os.makedirs(OUT, exist_ok=True)

def url(path):
    return "<?php echo esc_url( home_url( '%s' ) ); ?>" % path

def attrs(d):
    return (' ' + json.dumps(d, separators=(',', ':'), ensure_ascii=False)) if d else ''

def group(inner, cls='', tag='div', anchor=None):
    a = {}
    if tag != 'div': a['tagName'] = tag
    if anchor: a['anchor'] = anchor
    if cls: a['className'] = cls
    idattr = f' id="{anchor}"' if anchor else ''
    klass = 'wp-block-group' + (' ' + cls if cls else '')
    return f'<!-- wp:group{attrs(a)} -->\n<{tag}{idattr} class="{klass}">' + inner + f'</{tag}>\n<!-- /wp:group -->'

def p(text, cls=''):
    a = {'className': cls} if cls else {}
    c = f' class="{cls}"' if cls else ''
    return f'<!-- wp:paragraph{attrs(a)} -->\n<p{c}>{text}</p>\n<!-- /wp:paragraph -->'

def h(text, level=2, cls=''):
    a = {}
    if level != 2: a['level'] = level
    if cls: a['className'] = cls
    klass = 'wp-block-heading' + (' ' + cls if cls else '')
    return f'<!-- wp:heading{attrs(a)} -->\n<h{level} class="{klass}">{text}</h{level}>\n<!-- /wp:heading -->'

def button(label, href, cls=''):
    a = {'className': cls} if cls else {}
    klass = 'wp-block-button' + (' ' + cls if cls else '')
    return f'<!-- wp:button{attrs(a)} -->\n<div class="{klass}"><a class="wp-block-button__link wp-element-button" href="{href}">{label}</a></div>\n<!-- /wp:button -->'

def buttons(btns, cls=''):
    a = {'className': cls} if cls else {}
    klass = 'wp-block-buttons' + (' ' + cls if cls else '')
    return f'<!-- wp:buttons{attrs(a)} -->\n<div class="{klass}">' + '\n\n'.join(btns) + '</div>\n<!-- /wp:buttons -->'

def block(name, a=None):
    return f'<!-- wp:{name}{attrs(a or {})} /-->'

def pattern_ref(slug):
    return block('pattern', {'slug': slug})

def J(*parts):
    return '\n\n'.join(parts)

P = {}

P['hero'] = ('Home hero', 'jawad-sections', 'Rounded hero screen with headline, subtext, calls to action and speed ring.', group(group(group(J(
    block('jd/badges'),
    h('Turn visitors<br>into <strong>paying</strong><br>clients.', 1, 'jd-hero__title jd-split'),
    group(J(
        group(J(
            p('Your website should bring in calls, bookings and sales, not just look nice. I build fast WordPress and WooCommerce sites that make it easy for people to choose you, then keep them running so you can focus on the business.', 'jd-lead'),
            buttons([button('Get a fixed quote', url('/contact/'), 'jd-mag'), button('See the work', url('/work/'), 'is-style-ghost jd-mag')], 'is-large')), 'jd-hero__main'),
        block('jd/offer')), 'jd-hero__row')), 'jd-wrap'), 'jd-screen'), 'jd-hero', 'section'))

stats = [('10+', 'years building on WordPress'), ('500+', 'sites built, fixed or sped up'), ('24h', 'reply time, every weekday'), ('30d', 'free fixes after every launch')]
P['proof'] = ('Proof numbers', 'jawad-sections', 'Four large count-up numbers.', group(group(J(*[group(J(p(n, 'jd-stat__n'), p(t, 'jd-muted'))) for n, t in stats]), 'jd-proof'), 'jd-wrap', 'section'))

P['statement'] = ('Problem statement', 'jawad-sections', 'Large statement that lights up word by word on scroll.', group(J(
    p('The problem', 'jd-label'),
    p('Most business websites are slow, confusing, and quietly losing leads. I build sites that load in under two seconds, explain what you do at a glance, and make the <em>next step</em> obvious.', 'jd-statement__text jd-fill')), 'jd-section jd-wrap jd-statement', 'section'))

P['services-stack'] = ('Services stack', 'jawad-sections', 'Heading with sticky stacked service cards from the Services post type.', group(J(
    h('Four jobs.<br>Done properly.', 2, 'jd-section__title'),
    block('jd/services')), 'jd-section jd-wrap', 'section'))

P['work-track'] = ('Work track', 'jawad-sections', 'Heading with a horizontal pinned row of projects.', group(J(
    group(J(h('Recent<br>builds', 2, 'jd-section__title'), buttons([button('All projects', url('/work/'), 'is-style-ghost jd-mag')])), 'jd-wrap jd-section__head'),
    block('jd/projects')), 'jd-section jd-hwork', 'section'))

steps = [('01', '20-minute call', 'Goals, budget and deadline. A written fixed quote follows within 24 hours.'),
         ('02', 'Design first', "You approve the homepage design before any code. Revise until it's right."),
         ('03', 'Build on staging', 'A private preview link, updated as I go, with a short video at each milestone.'),
         ('04', 'Launch and hand over', 'Speed tested, forms checked, a training video for your team, and 30 days of free fixes.')]
P['process'] = ('Process', 'jawad-sections', 'Four-step process with a progress line drawn on scroll.', group(J(
    p('How a project runs', 'jd-label'),
    h('No surprises', 2, 'jd-section__title'),
    group(J(*[group(J(p(k, 'jd-step__k'), h(t, 3), p(d))) for k, t, d in steps]), 'jd-steps')), 'jd-section is-tight-top jd-wrap', 'section'))

P['testimonials'] = ('Testimonials', 'jawad-sections', 'Rotating client quotes.', group(J(
    p('Client words', 'jd-label'),
    block('jd/testimonials')), 'jd-section is-tight-top jd-wrap', 'section'))

P['pricing'] = ('Pricing', 'jawad-sections', 'Project packages from the Pricing post type.', group(J(
    group(J(h('Prices up front', 2, 'jd-section__title'), p('Fixed quotes, milestone payments, and revisions until the design is right.', 'jd-muted')), 'jd-section__head'),
    block('jd/pricing', {'group': 'project'})), 'jd-section is-tight-top jd-wrap', 'section', 'pricing'))

P['cta'] = ('Big call to action', 'jawad-sections', 'Full-width cobalt panel with the free audit offer.', group(group(J(
    p('Free quote · No obligation', 'jd-label'),
    h('Slow, broken,<br>or not selling?', 2, 'jd-cta__title'),
    p('Tell me what is wrong with your site, or what you want to build. You get a written fixed price and timeline within 24 hours.', 'jd-lead'),
    buttons([button('Get a fixed quote', url('/contact/'), 'jd-mag'), button('See the work', url('/work/'), 'is-style-ghost jd-mag')], 'is-large')), 'jd-wrap'), 'jd-cta', 'section'))

pay = [('Fixed quote', 'Written scope and price before any work starts. No hourly surprises.'),
       ('Milestones', 'Usually 40% to start, 40% at design approval, 20% at launch.'),
       ('Safe payment', 'Stripe invoice, or Upwork escrow if you want the extra protection.')]
P['pay'] = ('Payment terms', 'jawad-sections', 'How paying works, three columns.', group(J(
    p('How paying works', 'jd-label'),
    group(J(*[group(J(h(t, 3), p(d))) for t, d in pay]), 'jd-pay')), 'jd-section is-tight-top jd-wrap', 'section'))

P['faq-section'] = ('FAQ section', 'jawad-sections', 'Questions from the FAQs post type with FAQPage schema.', group(J(
    p('Questions clients ask first', 'jd-label'),
    block('jd/faq', {'group': 'general'})), 'jd-section is-tight-top jd-wrap jd-narrow', 'section'))

P['faq-home'] = ('Home FAQ', 'jawad-sections', 'Home page questions from the FAQs post type (group: home).', group(J(
    h('Questions,<br>answered', 2, 'jd-section__title'),
    block('jd/faq', {'group': 'home'})), 'jd-section is-tight-top jd-wrap jd-narrow', 'section'))

princ = [('Replies inside 24 hours', "Usually much faster. If I'm away, you know in advance and in writing."),
         ('Progress you can see', 'A staging link from week one and a short video update at every milestone.'),
         ('Plain English', "No jargon. You always know what's done, what's next, and why."),
         ('Everything is yours', 'Hosting, domain, logins and files sit in your accounts, not mine.'),
         ('Honest scope', "If a job isn't a fit for me, I'll say so on the first call and point you elsewhere."),
         ('30 days of free fixes', 'Anything I built that breaks after launch, I fix at no charge.')]
tools = ['WordPress', 'Elementor Pro', 'WooCommerce', 'ACF', 'Custom post types', 'PHP', 'JavaScript', 'Figma', 'WP Rocket', 'Rank Math', 'Cloudflare', 'Zapier', 'n8n', 'OpenAI API']

P['page-home'] = ('Home page', 'jawad-pages', 'Full home page layout.', J(
    pattern_ref('jawad/hero'), pattern_ref('jawad/proof'), pattern_ref('jawad/statement'), block('jd/marquee'),
    pattern_ref('jawad/services-stack'), pattern_ref('jawad/work-track'), pattern_ref('jawad/process'),
    pattern_ref('jawad/testimonials'), pattern_ref('jawad/pricing'), pattern_ref('jawad/faq-home'), pattern_ref('jawad/cta')))

P['page-about'] = ('About page', 'jawad-pages', 'About page with portrait, live clocks, principles and toolkit.', J(
    group(group(J(
        group(J(p('About', 'jd-label'), h('One developer.<br><em>No hand-offs.</em>', 1, 'jd-split'),
                p("I'm Jawad Ilyas. I've built WordPress sites for more than ten years, for everyone from startups and local service companies to established brands. Whatever the size, the goal is the same: a site that works hard without babysitting. When you hire me, the person on the call is the person writing the code.", 'jd-lead')), 'jd-about__text'),
        group(p('JI', 'jd-portrait__ini'), 'jd-portrait')), 'jd-about'), 'jd-phero jd-wrap', 'section'),
    group(block('jd/clocks'), 'jd-wrap', 'section'),
    group(J(p('What you can count on', 'jd-label'), group(J(*[group(J(h(t, 3), p(d))) for t, d in princ]), 'jd-princ')), 'jd-section jd-wrap', 'section'),
    group(J(p('Toolkit', 'jd-label'), group(J(*[p(t) for t in tools]), 'jd-tools')), 'jd-section is-tight-top jd-wrap', 'section'),
    pattern_ref('jawad/cta')))

P['page-contact'] = ('Contact page', 'jawad-pages', 'Contact page with the quote form and contact details.', J(
    group(J(p('Contact', 'jd-label'), h("Let's build<br><em>something good.</em>", 1, 'jd-split')), 'jd-phero jd-wrap', 'section'),
    group(group(J(block('jd/contact-form'), block('jd/contact-card')), 'jd-contact'), 'jd-wrap jd-contact-sec', 'section', 'contact')))

P['page-book'] = ('Book a call page', 'jawad-pages', 'Book a 20-minute call: intro, what we cover, and the embedded booking calendar.', J(
    group(J(p('Book a call', 'jd-label'), h("20 minutes.<br><em>Your project.</em>", 1, 'jd-split'),
            p("Pick a time that suits you and you'll get a Google Meet link straight away. We'll talk through what you need, and you'll leave with a clear next step, even if that isn't me.", 'jd-lead')), 'jd-phero jd-wrap', 'section'),
    group(group(J(
        group(J(p('What we cover', 'jd-label'),
                '<!-- wp:list {"ordered":true,"className":"jd-next"} -->\n<ol class="wp-block-list jd-next"><!-- wp:list-item -->\n<li>Your goals, and what the site needs to do for the business</li>\n<!-- /wp:list-item -->\n\n<!-- wp:list-item -->\n<li>What you have now: site, hosting, content, deadlines</li>\n<!-- /wp:list-item -->\n\n<!-- wp:list-item -->\n<li>An honest view on scope, timeline and budget</li>\n<!-- /wp:list-item -->\n\n<!-- wp:list-item -->\n<li>A written fixed quote within 24 hours of the call</li>\n<!-- /wp:list-item --></ol>\n<!-- /wp:list -->',
                p('Prefer writing? <a href="' + url('/contact/') + '">Send the details instead</a>.', 'jd-muted')), 'jd-box jd-book__side'),
        block('jd/booking')), 'jd-book'), 'jd-wrap jd-section is-tight-top', 'section', 'book')))

for slug, (title, cat, desc, content) in P.items():
    inserter = 'yes'
    header = f"""<?php
/**
 * Title: {title}
 * Slug: jawad/{slug}
 * Categories: {cat}
 * Description: {desc}
 * Inserter: {inserter}
 *
 * @package Jawad
 */

?>
"""
    open(os.path.join(OUT, slug + '.php'), 'w').write(header + content + '\n')
print('patterns:', len(P))
