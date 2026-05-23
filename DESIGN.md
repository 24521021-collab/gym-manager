---
name: Kinetic Performance System
colors:
  surface: '#131313'
  surface-dim: '#131313'
  surface-bright: '#393939'
  surface-container-lowest: '#0e0e0e'
  surface-container-low: '#1b1b1b'
  surface-container: '#1f1f1f'
  surface-container-high: '#2a2a2a'
  surface-container-highest: '#353535'
  on-surface: '#e2e2e2'
  on-surface-variant: '#e6bdbb'
  inverse-surface: '#e2e2e2'
  inverse-on-surface: '#303030'
  outline: '#ad8886'
  outline-variant: '#5d3f3e'
  surface-tint: '#ffb3b1'
  primary: '#ffb3b1'
  on-primary: '#680011'
  primary-container: '#e31837'
  on-primary-container: '#fffaf9'
  inverse-primary: '#bf0029'
  secondary: '#c6c6c7'
  on-secondary: '#2f3131'
  secondary-container: '#454747'
  on-secondary-container: '#b4b5b5'
  tertiary: '#c8c6c5'
  on-tertiary: '#313030'
  tertiary-container: '#747373'
  on-tertiary-container: '#fdfaf9'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#ffdad8'
  primary-fixed-dim: '#ffb3b1'
  on-primary-fixed: '#410007'
  on-primary-fixed-variant: '#92001d'
  secondary-fixed: '#e2e2e2'
  secondary-fixed-dim: '#c6c6c7'
  on-secondary-fixed: '#1a1c1c'
  on-secondary-fixed-variant: '#454747'
  tertiary-fixed: '#e5e2e1'
  tertiary-fixed-dim: '#c8c6c5'
  on-tertiary-fixed: '#1c1b1b'
  on-tertiary-fixed-variant: '#474746'
  background: '#131313'
  on-background: '#e2e2e2'
  surface-variant: '#353535'
typography:
  display-lg:
    fontFamily: Oswald
    fontSize: 72px
    fontWeight: '700'
    lineHeight: 80px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Oswald
    fontSize: 48px
    fontWeight: '600'
    lineHeight: 56px
  headline-lg-mobile:
    fontFamily: Oswald
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
  headline-md:
    fontFamily: Oswald
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
  body-lg:
    fontFamily: Montserrat
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Montserrat
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-bold:
    fontFamily: Montserrat
    fontSize: 14px
    fontWeight: '700'
    lineHeight: 20px
    letterSpacing: 0.05em
  stats-number:
    fontFamily: Oswald
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
spacing:
  base: 8px
  container-max: 1440px
  gutter: 24px
  margin-desktop: 64px
  margin-mobile: 20px
---

## Brand & Style

This design system embodies the "Athlete's Edge"—a high-performance aesthetic that merges the intensity of elite fitness with the sophistication of premium e-commerce. The brand personality is aggressive, professional, and uncompromising. It is designed to evoke a sense of urgency, discipline, and achievement.

The visual style is a hybrid of **High-Contrast Bold** and **Modern Glassmorphism**. We utilize deep obsidian backgrounds to create a sense of focused "night-mode" training environments, punctuated by high-energy crimson accents that signal action and heat. Glassmorphism is applied strategically to data overlays and dashboard widgets to maintain a sense of depth and technical precision without breaking the sleek, monolithic feel of the interface.

## Colors

The palette is rooted in a "True Black" foundation to maximize contrast and visual impact. 

- **Primary (#E31837):** A fiery, high-saturation red used exclusively for interactive elements, status indicators, and urgent brand callouts. It represents energy, heart rate, and movement.
- **Secondary (#FFFFFF):** Used for primary typography and high-level surface definitions to ensure maximum readability against dark backgrounds.
- **Surface & Background:** We use a tiered black system. `#000000` is the base canvas. `#1A1A1A` is used for elevated cards and containers.
- **Functional Grays:** Mid-tone grays are avoided in favor of low-opacity whites (e.g., White @ 10%) to maintain the glassmorphism effect and prevent the UI from looking muddy.

## Typography

The typography strategy relies on the tension between the condensed, vertical authority of **Oswald** and the open, geometric stability of **Montserrat**.

- **Headlines:** Always set in Oswald. For major marketing beats and section headers, use uppercase transformation with slight negative letter spacing to create a "packed" athletic look.
- **Body Text:** Montserrat provides the necessary legibility for complex data, workout descriptions, and product details. 
- **Data & Stats:** Use Oswald for numerical data in dashboards to evoke the feel of a digital scoreboard or stopwatch.
- **Accessibility:** On mobile devices, display type scales down aggressively to maintain the "all-in-one-view" dashboard philosophy.

## Layout & Spacing

This design system utilizes a **12-column fluid grid** for desktop and a **4-column grid** for mobile. The spacing rhythm is strictly based on an 8px square grid to ensure alignment of complex dashboard widgets and e-commerce grids.

- **Dashboard Layouts:** Use a "bento-box" style layout where widgets occupy spans of 3, 6, or 12 columns. 
- **E-commerce Grids:** Product listings use a standard 3-column layout on desktop to allow for large, high-impact product imagery.
- **Negative Space:** While the brand is high-energy, generous margins (64px+) are used between major sections to prevent the high-contrast colors from causing visual fatigue.

## Elevation & Depth

Depth is communicated through **Tonal Layering** and **Glassmorphism**, rather than traditional drop shadows.

- **Level 0 (Base):** True Black (#000000).
- **Level 1 (Cards/Widgets):** Deep Gray (#1A1A1A) with a subtle 1px inner border (White @ 10%) to define edges.
- **Level 2 (Overlays/Modals):** Glassmorphic surfaces using `backdrop-filter: blur(20px)` and a semi-transparent dark fill (Black @ 60%). This allows background energy/imagery to bleed through while maintaining text contrast.
- **Interactive States:** Primary buttons and active states use a "Glow" effect—a soft, primary-colored outer shadow (`0px 0px 20px rgba(227, 24, 55, 0.3)`) to simulate light emission from a screen.

## Shapes

The shape language is **Sharp (0)**. To reinforce the professional, "no-nonsense" athletic aesthetic, we utilize hard 90-degree angles for all primary containers, buttons, and input fields. 

- **Exceptions:** Circular shapes are reserved strictly for user avatars and specific progress rings (e.g., BMI gauges or activity rings) to provide a visual counterpoint to the rigid grid.
- **Borders:** Use thin, 1px strokes for all structural outlines. Heavy borders are avoided to keep the interface feeling premium and "lightweight" despite the dark color palette.

## Components

### Buttons & CTAs
Buttons are monolithic and rectangular. The **Primary Button** is solid Crimson (#E31837) with white uppercase text. The **Secondary Button** is a ghost style with a white 1px border. All buttons feature a high-speed hover transition (150ms).

### Cards
- **Fitness Cards:** Use full-bleed, high-contrast photography with a dark gradient overlay at the bottom to house white Oswald typography.
- **Product Cards:** Feature a neutral light-gray background for the product image to make the item "pop" against the dark UI, with a sharp Crimson "Add to Cart" bar appearing on hover.

### Dashboard & Analytics
- **Charts:** Use a monochromatic scale of the primary color. Lines should be thin and sharp; area charts should use a vertical gradient from Crimson to transparent.
- **Status Badges:** Small, rectangular tags. Use "Success" (Green), "Warning" (Yellow), and "Alert" (Crimson) but keep them desaturated to ensure the Primary brand red remains the dominant focal point.

### E-commerce Elements
- **Filters:** Use a sidebar layout with "Accordions" that have sharp edges.
- **Checkout:** A focused, single-column flow with glassmorphic order summaries on the right-hand side.

### Admin & Operations
- **Tables:** Stripped of vertical lines. Use subtle row zebra-striping (Black vs. Deep Gray) and Oswald for header labels to maintain the athletic character even in administrative views.