/* Portfolio-only presentation; intentionally isolated from the shared design system. */
.aroma-portfolio { --portfolio-accent: #df4937; --portfolio-ink: #202226; --portfolio-muted: #70757a; color:
var(--portfolio-ink); background: #fff; }
.aroma-portfolio *, .aroma-portfolio *::before, .aroma-portfolio *::after { box-sizing: border-box; }
.aroma-portfolio [hidden] { display: none !important; }
.aroma-portfolio__shell { width: min(100% - 3rem, 80rem); margin-inline: auto; }
.aroma-portfolio__hero { position: relative; overflow: hidden; padding: clamp(5rem,9vw,8rem) 0 1.5rem; background:
linear-gradient(120deg,#faf9f8 0%,#fff 53%,#f9f3f1 100%); }
.aroma-portfolio__hero-grid { position: absolute; inset: 0; opacity: .35; background-image: radial-gradient(#7c7e8020
.7px,transparent .7px); background-size: 17px 17px; mask-image: linear-gradient(90deg,#000,transparent 80%); }
.aroma-portfolio__hero-layout { position: relative; display: grid; grid-template-columns: minmax(0,1.05fr)
minmax(22rem,.95fr); align-items: center; gap: clamp(2rem,6vw,6rem); }
.aroma-portfolio__intro { max-width: 47rem; }
.aroma-portfolio__eyebrow { display: inline-flex; align-items: center; gap: .55rem; color: var(--portfolio-accent);
font-size: .68rem; font-weight: 750; letter-spacing: .14em; }
.aroma-portfolio__eyebrow i { width: .42rem; height: .42rem; border-radius: 50%; background: var(--portfolio-accent);
box-shadow: 0 0 0 .25rem #df49371a; }
.aroma-portfolio__intro h1 { max-width: 48rem; margin: 1.1rem 0 1.2rem; color: var(--portfolio-ink); font-size:
clamp(2.9rem,6vw,5.6rem); font-weight: 400; letter-spacing: -.065em; line-height: .99; }
.aroma-portfolio__intro > p { max-width: 39rem; margin: 0; color: var(--portfolio-muted); font-size:
clamp(1rem,1.4vw,1.14rem); line-height: 1.8; }
.aroma-portfolio__hero-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 1.3rem; margin-top: 2rem; }
.aroma-portfolio__button { display: inline-flex; align-items: center; justify-content: center; gap: 1.15rem; min-height:
3.25rem; padding: .8rem 1.15rem; border-radius: .4rem; font-size: .83rem; font-weight: 700; text-decoration: none;
transition: transform .18s ease,background-color .18s ease; }
.aroma-portfolio__button:hover { transform: translateY(-2px); }
.aroma-portfolio__button--primary { color: #fff; background: var(--portfolio-accent); }
.aroma-portfolio__button--primary:hover { color: #fff; background: #c53b2d; }
.aroma-portfolio__text-link { color: var(--portfolio-ink); font-size: .83rem; font-weight: 650; text-decoration: none; }
.aroma-portfolio__text-link:hover { color: var(--portfolio-accent); }
.aroma-portfolio__hero-art { position: relative; min-height: 26rem; display: grid; place-items: center; isolation:
isolate; }
.aroma-portfolio__hero-art::before { position: absolute; z-index: -1; width: 19rem; height: 19rem; border-radius: 50%;
background: #df493719; filter: blur(35px); content: ""; }
.aroma-portfolio__orbit { position: absolute; width: min(100%,26rem); aspect-ratio: 1; border: 1px dashed #292c301c;
border-radius: 50%; }
.aroma-portfolio__orbit--two { width: min(80%,20rem); border-style: solid; border-color: #df493725; transform:
rotate(26deg) scaleY(.77); }
.aroma-portfolio__art-core { position: relative; display: grid; place-items: center; align-content: center; width:
10rem; height: 10rem; border: 1px solid #ffffffd9; border-radius: 2.4rem; color: #fff; background:
linear-gradient(145deg,#303236,#17191c); box-shadow: 0 28px 60px #16191d30; transform: rotate(-8deg); }
.aroma-portfolio__art-core span { color: #ff8e7e; font-size: .65rem; font-weight: 750; letter-spacing: .22em;
}.aroma-portfolio__art-core b { margin-top: .7rem; font-size: 1.15rem; letter-spacing: .06em; line-height: 1.12;
text-align: center; }.aroma-portfolio__art-core i { position: absolute; right: -1rem; bottom: -.65rem; display: grid;
place-items: center; width: 2.8rem; height: 2.8rem; border-radius: .8rem; color: #fff; background:
var(--portfolio-accent); font-size: 1.3rem; font-style: normal; }
.aroma-portfolio__art-node { position: absolute; display: flex; align-items: center; gap: .55rem; padding: .62rem
.78rem; border: 1px solid #22242612; border-radius: .5rem; background: #ffffffed; box-shadow: 0 12px 26px #15191d12;
color: #34383c; font-size: .66rem; }.aroma-portfolio__art-node span { display: grid; place-items: center; width:
1.65rem; height: 1.65rem; border-radius: .4rem; color: var(--portfolio-accent); background: #fff0ed; font-size: .95rem;
}.aroma-portfolio__art-node--app { top: 13%; left: 7%; animation: portfolio-drift 5.5s ease-in-out infinite;
}.aroma-portfolio__art-node--network { top: 25%; right: 0; animation: portfolio-drift 6s ease-in-out -1.5s infinite;
}.aroma-portfolio__art-node--secure { right: 4%; bottom: 18%; animation: portfolio-drift 5.8s ease-in-out -3s infinite;
}.aroma-portfolio__art-node--robot { bottom: 11%; left: 3%; animation: portfolio-drift 6.3s ease-in-out -2s infinite;
}.aroma-portfolio__art-caption { position: absolute; bottom: 0; color: #8b8f93; font-size: .55rem; font-weight: 700;
letter-spacing: .15em; }
.aroma-portfolio__hero-foot { position: relative; display: flex; justify-content: space-between; margin-top: 1.5rem;
padding-top: 1rem; border-top: 1px solid #25282b17; color: #888d91; font-size: .58rem; font-weight: 750; letter-spacing:
.14em; }
.aroma-portfolio__catalog { padding: clamp(4rem,7vw,6rem) 0; color: var(--portfolio-ink); background: #fff; }
.aroma-portfolio__catalog-heading { display: flex; align-items: end; justify-content: space-between; gap: 3rem; }
.aroma-portfolio__eyebrow--light { color: #ff9585; }
.aroma-portfolio__catalog-heading h2 { margin: .9rem 0 0; color: var(--portfolio-ink); font-size:
clamp(2.35rem,4.7vw,4rem); font-weight: 620; letter-spacing: -.055em; line-height: 1.03;
}.aroma-portfolio__catalog-heading h2 span { color: var(--portfolio-accent); }
.aroma-portfolio__catalog-heading > p { max-width: 31rem; margin: 0 0 .2rem; color: var(--portfolio-muted); font-size:
.94rem; line-height: 1.75; }
.aroma-portfolio__filters { display: flex; flex-wrap: wrap; gap: .55rem; margin: 2.3rem 0 1.3rem; padding-bottom:
1.25rem; border-bottom: 1px solid #25282b20; }
.aroma-portfolio__filter { min-height: 2.55rem; padding: .55rem .9rem; border: 1px solid #25282b2e; border-radius: 99px;
color: #50555a; background: #fff; font: inherit; font-size: .73rem; font-weight: 650; cursor: pointer; transition: color
.16s ease,border-color .16s ease,background .16s ease; }.aroma-portfolio__filter:hover { border-color:
var(--portfolio-accent); color: var(--portfolio-ink); }.aroma-portfolio__filter.is-active { border-color:
var(--portfolio-accent); color: #fff; background: var(--portfolio-accent); }
.aroma-portfolio__category-intro { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: .6rem;
margin-bottom: 1.5rem; }.aroma-portfolio__category-intro > div { display: grid; grid-template-columns: 2rem 1fr;
align-items: center; column-gap: .65rem; padding: .8rem .9rem; border: 1px solid #e7e7e6; border-radius: .45rem;
background: #faf9f8; }.aroma-portfolio__category-intro span { grid-row: span 2; color: var(--portfolio-accent);
font-size: .65rem; font-weight: 750; }.aroma-portfolio__category-intro b { color: var(--portfolio-ink); font-size:
.73rem; }.aroma-portfolio__category-intro small { margin-top: .2rem; color: #757a7e; font-size: .61rem; }
.aroma-portfolio__grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 1rem; align-items: stretch;
}
.aroma-portfolio__card { min-width: 0; overflow: hidden; border: 1px solid #e6e6e5; border-radius: .55rem; background:
#fff; transition: transform .2s ease,border-color .2s ease,box-shadow .2s ease; }.aroma-portfolio__card:hover {
transform: translateY(-4px); border-color: #df49375a; box-shadow: 0 18px 35px #090a0c16; }.aroma-portfolio__card-link {
display: block; height: 100%; color: inherit; text-decoration: none;
}.aroma-portfolio__card-link:focus-visible,.aroma-portfolio__filter:focus-visible,.aroma-portfolio__button:focus-visible
{ outline: 2px solid var(--portfolio-accent); outline-offset: 3px; }
.aroma-portfolio__visual { position: relative; display: grid; place-items: center; height: 12.5rem; overflow: hidden;
background: #34373b; }.aroma-portfolio__visual--coral { background: linear-gradient(140deg,#643a36,#332c2e);
}.aroma-portfolio__visual--green { background: linear-gradient(140deg,#34483d,#292f2d);
}.aroma-portfolio__visual--violet { background: linear-gradient(140deg,#403b55,#2c2b36); }.aroma-portfolio__visual--blue
{ background: linear-gradient(140deg,#334954,#292e33); }.aroma-portfolio__visual--amber { background:
linear-gradient(140deg,#574938,#302e2a); }.aroma-portfolio__visual-grid { position: absolute; inset: 0; opacity: .26;
background-image: linear-gradient(#ffffff10 1px,transparent 1px),linear-gradient(90deg,#ffffff10 1px,transparent 1px);
background-size: 25px 25px; mask-image: radial-gradient(ellipse,#000,transparent 78%); }.aroma-portfolio__visual-index {
position: absolute; top: .9rem; left: 1rem; color: #ffffffa8; font-size: .56rem; font-weight: 750; letter-spacing:
.12em; }.aroma-portfolio__illustration { position: relative; width: 72%; height: 75%; overflow: visible;
}.aroma-portfolio__illustration > path,.aroma-portfolio__illustration > rect,.aroma-portfolio__illustration > circle {
stroke: #f4f3f0; stroke-width: 2.3; stroke-linecap: round; stroke-linejoin: round;
}.aroma-portfolio__illustration-accent { stroke: #ff9281 !important; }.aroma-portfolio__illustration-fill { fill:
#ff9281; stroke: #ff9281; }.aroma-portfolio__visual-caption { position: absolute; right: .75rem; bottom: .7rem; padding:
.36rem .5rem; border: 1px solid #ffffff35; border-radius: 99px; color: #fff; background: #17191c80; font-size: .55rem;
font-weight: 650; backdrop-filter: blur(7px); }.aroma-portfolio__visual img { width: 100%; height: 100%; object-fit:
cover; transition: transform .4s ease; }.aroma-portfolio__card:hover .aroma-portfolio__visual img { transform:
scale(1.035); }
.aroma-portfolio__card-body { padding: 1.1rem 1.15rem 1.25rem; }.aroma-portfolio__card-meta { display: flex;
align-items: center; justify-content: space-between; color: var(--portfolio-accent); font-size: .58rem; font-weight:
750; letter-spacing: .1em; text-transform: uppercase; }.aroma-portfolio__card-meta span:last-child { color: #92969a;
}.aroma-portfolio__card-body h3 { margin: .7rem 0 .45rem; color: var(--portfolio-ink); font-size: 1.05rem; font-weight:
680; letter-spacing: -.02em; line-height: 1.3; }.aroma-portfolio__card-body > p { min-height: 4.2rem; margin: 0; color:
#666c71; font-size: .78rem; line-height: 1.7; }.aroma-portfolio__tags { display: flex; flex-wrap: wrap; gap: .35rem;
margin-top: .9rem; }.aroma-portfolio__tags span { padding: .34rem .48rem; border: 1px solid #e3e4e5; border-radius:
.25rem; color: #555b60; font-size: .59rem; line-height: 1; }.aroma-portfolio__card-more { display: inline-flex;
align-items: center; gap: .5rem; margin-top: .85rem; color: var(--portfolio-accent); font-size: .7rem; font-weight: 700;
}.aroma-portfolio__empty { margin: 1.5rem 0; color: #555b60; text-align: center; }
.aroma-portfolio__note { padding: 1.2rem 0; background: #f5f4f2; }.aroma-portfolio__note-inner { display: flex;
align-items: center; gap: 1rem; }.aroma-portfolio__note-mark { display: grid; place-items: center; flex: 0 0 2.4rem;
height: 2.4rem; border-radius: .55rem; color: var(--portfolio-accent); background: #df493718; font-size: 1.2rem;
}.aroma-portfolio__note-inner p { flex: 1; margin: 0; color: #555a5e; font-size: .84rem; line-height: 1.6;
}.aroma-portfolio__note-inner a { color: var(--portfolio-ink); font-size: .78rem; font-weight: 700; text-decoration:
none; white-space: nowrap; }.aroma-portfolio__note-inner a:hover { color: var(--portfolio-accent); }
@keyframes portfolio-drift { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
@media (max-width: 960px) { .aroma-portfolio__hero-layout { grid-template-columns: minmax(0,1fr) minmax(18rem,.82fr);
gap: 1.5rem; }.aroma-portfolio__hero-art { min-height: 22rem; }.aroma-portfolio__grid { grid-template-columns:
repeat(2,minmax(0,1fr)); }.aroma-portfolio__catalog-heading { align-items: start; flex-direction: column; gap: 1.2rem;
}.aroma-portfolio__catalog-heading > p { max-width: 42rem; } }
@media (max-width: 640px) { .aroma-portfolio__shell { width: min(100% - 2rem,80rem); }.aroma-portfolio__hero {
padding-top: 4rem; }.aroma-portfolio__hero-layout { grid-template-columns: minmax(0,1fr); }.aroma-portfolio__intro h1 {
font-size: clamp(2.7rem,12vw,4.1rem); }.aroma-portfolio__hero-art { min-height: 20rem; max-width: 28rem; width: 100%;
margin: 0 auto; }.aroma-portfolio__orbit { width: 19rem; }.aroma-portfolio__orbit--two { width: 15rem;
}.aroma-portfolio__art-node { font-size: .58rem; }.aroma-portfolio__art-node--network { right: -.3rem;
}.aroma-portfolio__hero-foot { font-size: .49rem; }.aroma-portfolio__catalog { padding-block: 3.5rem;
}.aroma-portfolio__catalog-heading h2 { font-size: 2.5rem; }.aroma-portfolio__filters { flex-wrap: nowrap; overflow-x:
auto; padding: .3rem .1rem 1rem; scrollbar-width: thin; }.aroma-portfolio__filter { flex: 0 0 auto;
}.aroma-portfolio__category-intro { grid-template-columns: minmax(0,1fr); }.aroma-portfolio__grid {
grid-template-columns: minmax(0,1fr); }.aroma-portfolio__visual { height: 13rem; }.aroma-portfolio__card-body > p {
min-height: 0; }.aroma-portfolio__note-inner { align-items: flex-start; flex-wrap: wrap; }.aroma-portfolio__note-inner p
{ flex-basis: calc(100% - 3.5rem); }.aroma-portfolio__note-inner a { margin-left: 3.4rem; } }
@media (prefers-reduced-motion: reduce) { .aroma-portfolio *, .aroma-portfolio *::before, .aroma-portfolio *::after {
animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important;
transition-duration: .01ms !important; } }