import Reveal from './components/Reveal';
import { HeartIcon, SearchIcon, StarIcon } from './components/icons';
import StoryFooter from './components/StoryFooter';
import StoryHeader from './components/StoryHeader';
import WhatsAppButton from './components/WhatsAppButton';
import { FONTS, STORY_IMAGES } from './theme';
import { resolveAsset } from './utils';

const FOCUS_RING =
    'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#B86B4B]';

const VALUES = [
    {
        icon: HeartIcon,
        title: 'Made Wholeheartedly',
        description:
            'Every piece we create is born from a sincere intention. It is more than just a product; it is an expression of genuine love for the art of handcrafting.',
    },
    {
        icon: SearchIcon,
        title: 'Meaningful Details',
        description:
            'We believe that true beauty lies hidden in the smallest details. Every knot and every choice of thread color is carefully selected to create perfect harmony.',
    },
    {
        icon: StarIcon,
        title: 'Unique and Personal',
        description:
            'No two Decak pieces are exactly alike. Each product is designed to celebrate individual uniqueness because everyone deserves to own something that is truly their own.',
    },
];

function DisplayHeading({ as: Tag = 'h2', className = '', children, ...props }) {
    return (
        <Tag
            className={`font-semibold ${className}`}
            style={{ fontFamily: FONTS.display }}
            {...props}
        >
            {children}
        </Tag>
    );
}

/**
 * Font links are rendered through React 19's document metadata support,
 * so the component also works outside the Laravel Blade entry point.
 */
function StoryFonts() {
    return (
        <>
            <link rel="preconnect" href="https://fonts.googleapis.com" />
            <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
            <link
                rel="stylesheet"
                href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap"
                precedence="default"
            />
        </>
    );
}

function HeroSection({ image }) {
    return (
        <section
            aria-labelledby="story-hero-title"
            className="grid min-h-[620px] grid-cols-1 bg-[#F9F6F1] md:grid-cols-2"
        >
            <div className="story-hero-media min-h-[400px] overflow-hidden bg-[#E7E1D7] md:min-h-[620px]">
                {/* Foto: /images/story/hero-keychain-in-hand.jpg */}
                <img
                    src={image(STORY_IMAGES.hero)}
                    alt="Hand holding a purple floral crochet keychain by Decak Handmade"
                    className="block h-full w-full object-cover"
                    loading="eager"
                    fetchPriority="high"
                    decoding="async"
                />
            </div>

            <div className="flex flex-col justify-center px-8 py-16 md:px-16 lg:px-24">
                <DisplayHeading
                    as="h1"
                    id="story-hero-title"
                    className="story-hero-heading mb-6 text-4xl leading-[1.15] tracking-tight text-[#2B2520] lg:text-5xl"
                >
                    Every Work Has a Story
                </DisplayHeading>

                <p className="story-hero-copy mb-8 max-w-md text-sm leading-relaxed text-[#5C554F]">
                    Decak Handmade was born from a deep love for genuine craftsmanship. Every stitch, every
                    knot, and every choice of material reflects our intention: to create meaningful pieces that
                    stand the test of time.
                </p>

                <a
                    href="#starting-from-simple-things"
                    className={`story-hero-cta w-fit rounded-[4px] bg-[#B86B4B] px-6 py-3 text-xs font-medium text-white transition-colors hover:bg-[#9E583B] ${FOCUS_RING}`}
                >
                    Get to Know Us Better
                </a>
            </div>
        </section>
    );
}

function StartingSection({ image }) {
    return (
        <section
            id="starting-from-simple-things"
            aria-labelledby="starting-title"
            className="scroll-mt-24 bg-[#EFECE6] px-8 py-24 md:px-16 lg:px-24"
        >
            <div className="mx-auto grid max-w-6xl grid-cols-1 items-center gap-12 md:grid-cols-12">
                <div className="md:col-span-6">
                    <Reveal>
                        <DisplayHeading
                            as="h2"
                            id="starting-title"
                            className="text-3xl leading-tight tracking-tight text-[#2B2520] lg:text-4xl"
                        >
                            Starting from Simple Things
                        </DisplayHeading>
                    </Reveal>

                    <Reveal
                        variant="line"
                        className="my-6 h-[1.5px] w-10 bg-[#B86B4B]"
                        delay={100}
                    />

                    <Reveal
                        as="p"
                        className="max-w-prose text-sm leading-relaxed text-[#5C554F]"
                        delay={180}
                    >
                        Driven by a deep passion for sewing and the rich textile heritage of the Indonesian
                        archipelago, Eka Wahyu Setyowati founded Decak Handmade in Surabaya in 2019. She
                        transforms batik scraps, lurik (traditional handwoven fabric), and ecoprint textiles into
                        home decor and functional accessories imbued with high artistic value.
                    </Reveal>

                    <Reveal
                        as="p"
                        className="mt-4 max-w-prose text-sm leading-relaxed text-[#5C554F]"
                        delay={260}
                    >
                        From humble beginnings characterized by a unique blend of modern design and ethnic
                        touches this home based business has steadily grown and expanded its reach. Today, its
                        creations not only adorn interiors across Indonesia but have also ventured into the
                        international market, reaching Canada, Turkey, and India through exhibitions for micro,
                        small, and medium-sized enterprises (MSMEs).
                    </Reveal>
                </div>

                <Reveal
                    variant="curtain-right"
                    className="story-media overflow-hidden md:col-span-6"
                    delay={120}
                >
                    {/* Foto: /images/story/crochet-keychain-flower.jpg */}
                    <img
                        src={image(STORY_IMAGES.keychainFlower)}
                        alt="Olive green crochet keychain with pink floral pattern on a neutral background"
                        className="block aspect-square w-full border border-[#DCD5CB] bg-[#E7E1D7] object-cover"
                        loading="lazy"
                        decoding="async"
                    />
                </Reveal>
            </div>
        </section>
    );
}

function MadeWithHeartSection({ image }) {
    return (
        <section aria-labelledby="made-with-heart-title" className="bg-[#F9F6F1] pb-24">
            <Reveal
                variant="curtain-down"
                className="story-media overflow-hidden border-y border-[#DCD5CB] bg-[#E7E1D7]"
            >
                {/* Foto: /images/story/workshop-tools.jpg */}
                <img
                    src={image(STORY_IMAGES.workshop)}
                    alt="Sewing tools and craft supplies neatly arranged on a workbench"
                    className="block h-[360px] w-full object-cover md:h-[420px]"
                    loading="lazy"
                    decoding="async"
                />
            </Reveal>

            <div className="mx-auto grid max-w-6xl grid-cols-1 items-start gap-8 px-8 pt-14 md:grid-cols-12 md:px-16">
                <div className="md:col-span-5">
                    <Reveal>
                        <DisplayHeading
                            as="h2"
                            id="made-with-heart-title"
                            className="text-3xl leading-tight tracking-tight text-[#2B2520] lg:text-4xl"
                        >
                            Made with Heart
                        </DisplayHeading>
                    </Reveal>
                </div>

                <div className="md:col-span-7">
                    <Reveal
                        as="p"
                        className="max-w-prose text-sm leading-relaxed text-[#5C554F]"
                        delay={100}
                    >
                        Behind every Decak Handmade product lie hours of meticulous care: selecting the finest
                        materials, determining the precise patterns, and refining every detail with infinite
                        patience. This is more than just work it is a calling. Each piece we create is the result
                        of creativity that is constantly honed, alongside an unwavering commitment to quality and
                        true beauty.
                    </Reveal>
                </div>
            </div>
        </section>
    );
}

function ValuesSection() {
    return (
        <section aria-labelledby="values-title" className="bg-[#EFECE6] px-8 py-24 md:px-16">
            <div className="mx-auto max-w-6xl">
                <Reveal>
                    <DisplayHeading
                        as="h2"
                        id="values-title"
                        className="mb-16 text-center text-3xl tracking-tight text-[#2B2520] lg:text-4xl"
                    >
                        Values We Uphold
                    </DisplayHeading>
                </Reveal>

                <div className="grid grid-cols-1 gap-10 md:grid-cols-3">
                    {VALUES.map(({ icon: Icon, title, description }, index) => (
                        <Reveal
                            key={title}
                            className={index > 0 ? 'md:border-l md:border-[#D6CFC5] md:pl-10' : ''}
                            delay={index * 100}
                        >
                            <Icon className="mb-4 h-5 w-5 text-[#B86B4B]" strokeWidth={1.5} />

                            <DisplayHeading
                                as="h3"
                                className="text-xl leading-snug tracking-tight text-[#2B2520] lg:text-2xl"
                            >
                                {title}
                            </DisplayHeading>

                            <p className="mt-3 text-sm leading-relaxed text-[#5C554F]">{description}</p>
                        </Reveal>
                    ))}
                </div>
            </div>
        </section>
    );
}

function BentoGallerySection({ image, catalogUrl }) {
    return (
        <section aria-labelledby="bento-title" className="bg-[#F9F6F1] px-8 py-24 md:px-16">
            <div className="mx-auto max-w-5xl">
                <Reveal>
                    <DisplayHeading
                        as="h2"
                        id="bento-title"
                        className="mb-10 text-3xl tracking-tight text-[#2B2520] lg:text-4xl"
                    >
                        Small in Shape, Big in Meaning
                    </DisplayHeading>
                </Reveal>

                <div className="grid grid-cols-1 gap-5 md:grid-cols-12">
                    <Reveal
                        variant="image"
                        className="story-media overflow-hidden border border-[#DCD5CB] bg-[#E7E1D7] md:col-span-5"
                    >
                        {/* Foto: /images/story/team-conversation.jpg */}
                        <img
                            src={image(STORY_IMAGES.team)}
                            alt="Three people discussing around a cafe table with laptops and notebooks"
                            className="block h-full min-h-[380px] w-full object-cover"
                            loading="lazy"
                            decoding="async"
                        />
                    </Reveal>

                    <div className="flex flex-col gap-5 md:col-span-7">
                        <Reveal
                            variant="image"
                            className="story-media overflow-hidden border border-[#DCD5CB] bg-[#E7E1D7]"
                            delay={90}
                        >
                            {/* Foto: /images/story/living-room.jpg */}
                            <img
                                src={image(STORY_IMAGES.livingRoom)}
                                alt="Neutral-toned living room styled with a sofa, rug, and woven decor"
                                className="block h-[210px] w-full object-cover"
                                loading="lazy"
                                decoding="async"
                            />
                        </Reveal>

                        <Reveal
                            variant="image"
                            className="story-media overflow-hidden border border-[#DCD5CB] bg-[#E7E1D7]"
                            delay={180}
                        >
                            {/* Foto: /images/story/clothing-flatlay.jpg */}
                            <img
                                src={image(STORY_IMAGES.flatlay)}
                                alt="Knitwear and jeans styled flat on white linen with a tulip stem"
                                className="block h-[210px] w-full object-cover"
                                loading="lazy"
                                decoding="async"
                            />
                        </Reveal>
                    </div>
                </div>

                <div className="mt-8 flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <Reveal
                        as="p"
                        className="text-xs italic text-[#7A726B]"
                        delay={260}
                    >
                        Created to accompany your daily life.
                    </Reveal>

                    <Reveal
                        as="a"
                        href={catalogUrl}
                        className={`rounded-[4px] border border-[#B86B4B] px-5 py-2.5 text-xs text-[#B86B4B] transition-colors hover:bg-[#B86B4B] hover:text-white ${FOCUS_RING}`}
                        delay={340}
                    >
                        See All Products
                    </Reveal>
                </div>
            </div>
        </section>
    );
}

function KeepCreatingSection({ image }) {
    return (
        <section
            aria-labelledby="keep-creating-title"
            className="grid min-h-[560px] grid-cols-1 bg-[#EFECE6] md:grid-cols-2"
        >
            <Reveal
                variant="curtain-left"
                className="story-media min-h-[380px] overflow-hidden border-r border-[#DCD5CB] bg-[#E7E1D7]"
            >
                {/* Foto: /images/story/kraft-tote.jpg */}
                <img
                    src={image(STORY_IMAGES.kraftTote)}
                    alt="Plain kraft tote bag with handles on a light grey background"
                    className="block h-full min-h-[380px] w-full object-cover"
                    loading="lazy"
                    decoding="async"
                />
            </Reveal>

            <div className="flex flex-col justify-center px-8 py-16 md:px-16 lg:px-24">
                <Reveal>
                    <DisplayHeading
                        as="h2"
                        id="keep-creating-title"
                        className="mb-6 text-3xl leading-tight tracking-tight text-[#2B2520] lg:text-4xl"
                    >
                        Keep Creating, Bring Meaning
                    </DisplayHeading>
                </Reveal>

                <Reveal
                    as="p"
                    className="max-w-prose text-sm leading-relaxed text-[#5C554F]"
                >
                    We dream of remaining a home for authentic Indonesian handicrafts—not merely selling
                    products, but preserving traditions, honoring the creative process, and inspiring more people
                    to discover beauty in simple things crafted with love.
                </Reveal>

                <Reveal
                    as="p"
                    className="mt-4 max-w-prose text-sm leading-relaxed text-[#5C554F]"
                    delay={80}
                >
                    Every piece we create is a small step toward a more beautiful world—one where precision is
                    valued, uniqueness is celebrated, and meaning is always present behind every object.
                </Reveal>
            </div>
        </section>
    );
}

export default function OurStoryPage({
    homeUrl = '/home',
    catalogUrl = '/catalog',
    storyUrl = '/our-story',
    userUrl = '/user',
    cartUrl = '/user/cart',
    testimonialUrl = '/home#testimoni',
    assetUrl = '/',
}) {
    const image = (path) => resolveAsset(assetUrl, path);

    return (
        <div
            className="story-page min-h-screen bg-[#F9F6F1] text-[#5C554F] antialiased selection:bg-[#B86B4B] selection:text-white"
            style={{ fontFamily: FONTS.body }}
        >
            <StoryFonts />

            <StoryHeader
                homeUrl={homeUrl}
                catalogUrl={catalogUrl}
                storyUrl={storyUrl}
                userUrl={userUrl}
                cartUrl={cartUrl}
            />

            <main>
                <HeroSection image={image} />
                <StartingSection image={image} />
                <MadeWithHeartSection image={image} />
                <ValuesSection />
                <BentoGallerySection image={image} catalogUrl={catalogUrl} />
                <KeepCreatingSection image={image} />
            </main>

            <StoryFooter
                homeUrl={homeUrl}
                catalogUrl={catalogUrl}
                storyUrl={storyUrl}
                testimonialUrl={testimonialUrl}
            />

            <WhatsAppButton />
        </div>
    );
}
