@extends('layouts.app')

@section('title', 'Massar - Discover Egypt\'s Hidden Gems')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="hero-overlay">
        <h1>Explore Egypt's<br>Ancient Wonders</h1>
        <p class="hero-desc">Unveil the mysteries of the Pyramids, timeless temples,<br>and the rich heritage of pharaohs. Embark on a journey<br>where history comes alive in the land of the Nile.</p>
    </div>
        <!-- Scroll Down Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white text-center">
        <p class="mb-2 text-sm uppercase tracking-widest">Scroll Down</p>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
    </div>
</section>

<main>
    <section class="about-us">
        <div class="about-text">
            <h1>About us</h1>
            <p>Our mission is to ignite the spirit of discovery in every traveler's heart, offering meticulously crafted itineraries that blend adrenaline-pumping activities with awe-inspiring landscapes across Egypt. With a team of seasoned globetrotters, we ensure that every expedition is infused with excitement, allowing you to experience the wonders of this ancient land. Embark on a voyage of a lifetime with us, as we redefine the art of exploration in Egypt.</p>
            <button class="btn-read">Read more »</button>
        </div>
        <div class="about-img">
            <img src="https://i.pinimg.com/736x/34/b5/1c/34b51ca98afa53170d2fb38f6ea3d7ac.jpg" alt="Pyramids of Egypt">
        </div>
    </section>

    <!-- Featured Attractions Section -->
<section class="container mx-auto px-4 mb-16">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-700">Featured Attractions</h2>
            <p class="text-gray-500 mt-2">Handpicked experiences you shouldn't miss</p>
        </div>
    </div>

        <div class="attractions-grid">
            @forelse($featured as $attraction)
            <div class="card group hover:shadow-xl transition-all duration-300">
                <div class="relative overflow-hidden">
                    <img src="{{ $attraction['image'] }}" alt="{{ $attraction['title'] }}" class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute top-4 left-4 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full">
                        <p class="text-gray-600 font-medium">
                            From {{ $attraction['price'] }}£E<span class="text-sm">/person</span>
                        </p>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold text-gray-600">{{ $attraction['title'] }}</h3>
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ $attraction['rating'] }} ({{ number_format($attraction['reviewCount']) }})</span>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4 line-clamp-2">{{ $attraction['description'] }}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">{{ $attraction['location'] }} • {{ $attraction['duration'] }}</span>
                        <a href="{{ route('attractions.show', $attraction['slug']) }}" class="btn-primary">View Details</a>
                    </div>
                </div>
            </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-600 mb-2">No attractions found</h2>
                    <p class="text-gray-500 mb-6">There are currently no attractions available. Please check back later.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!--Top Attractions Section -->
    <section class="top-attractions">
    <h2>Top Attractions</h2>
    <div class="attractions-grid">
        <!-- Attraction 1 -->
        <div class="attraction-card">
          <img src="https://i.pinimg.com/736x/e8/f9/a0/e8f9a0ecf7322d15370373352aee6636.jpg" alt="Karnak Temple">
          <div class="attraction-info">
            <span class="category">Historical Site</span>
            <h3>Karnak Temple</h3>
            <button class="btn-explore">Explore more »</button>
          </div>
        </div>
        <!-- Attraction 2 -->
        <div class="attraction-card">
          <img src="https://i.pinimg.com/736x/35/3a/89/353a89890413e8f0fce5ea24497e96a3.jpg" alt="Luxor">
          <div class="attraction-info">
            <span class="category">Historical Site</span>
            <h3>Luxor</h3>
            <button class="btn-explore">Explore more »</button>
          </div>
        </div>
        <!-- Attraction 3 -->   
        <div class="attraction-card">
          <img src="https://i.pinimg.com/736x/60/f3/0c/60f30cdc0f44674a0a2b1e6c2c312f4d.jpg" alt="Giza Pyramids">
          <div class="attraction-info">
            <span class="category">Historical Site</span>
            <h3>Giza Pyramids</h3>
            <button class="btn-explore">Explore more »</button>
          </div>
        </div>
        <!-- Attraction 4 -->
        <div class="attraction-card">
          <img src="https://i.pinimg.com/736x/fa/05/48/fa0548c00e03dc1e7507f1ffa8cb063a.jpg" alt="Coptic Cairo">
          <div class="attraction-info">
            <span class="category">Museums</span>
            <h3>Coptic Cairo</h3>
            <button class="btn-explore">Explore more »</button>
          </div>
        </div>
        <!-- Attraction 5 -->   
        <div class="attraction-card">
          <img src="https://i.pinimg.com/736x/fe/43/3f/fe433f5b8066236cef2e567ed3fe5d27.jpg" alt="Al-Azhar Mosque">
          <div class="attraction-info">
            <span class="category">Museums</span>
            <h3>Al-Azhar Mosque</h3>
            <button class="btn-explore">Explore more »</button>
          </div>
        </div>
        <!-- Attraction 6 -->
        <div class="attraction-card">
          <img src="https://i.pinimg.com/736x/7f/c1/bf/7fc1bf3a6ff3ba5899154a51132ce405.jpg" alt="Egyptian Museum">
          <div class="attraction-info">
            <span class="category">Museums</span>
            <h3>Egyptian Museum</h3>
            <button class="btn-explore">Explore more »</button>
          </div>
        </div>
      </div>
    </section>
    

    <section class="cultural-experiences">
        <h2>Cultural Experiences</h2>
        <div class="culture-grid">
            <div class="culture-card" style="background-image:url('https://cdn.prod.website-files.com/642024d9cc09c9be69c4268e/66c32626fc3ce2ff73af3545_656de83345da97a66820f8f4_Mezquita.jpeg')">
                <span>Mohammed Ali Mosque</span>
            </div>
            <div class="culture-card" style="background-image:url('https://i.pinimg.com/736x/03/ff/f9/03fff98f46114274459b324bde6eaa16.jpg')">
                <span>Luxor</span>
            </div>
            <div class="culture-card" style="background-image:url('https://i.pinimg.com/736x/09/c2/74/09c27459d36a567901202763f5fea3e5.jpg')">
                <span>Egyptian Festivals and Celebrations</span>
            </div>
            <div class="culture-card" style="background-image:url('https://i.pinimg.com/736x/2f/64/33/2f64336326b3e74a6ae6b3810f096ec4.jpg')">
                <span>Khan-al khalili</span>
            </div>
            <div class="culture-card" style="background-image:url('https://i.pinimg.com/736x/82/c2/24/82c224c5b0df3fac75a0dbf5e2209c62.jpg')">
                <span>Saladin Citadel</span>
            </div>
            <div class="culture-card" style="background-image:url('https://i.pinimg.com/736x/0b/d0/1e/0bd01edba0afb66f808099504ad2cf33.jpg')">
                <span>Al-Muizz Street</span>
            </div>
            <div class="culture-card" style="background-image:url('https://i.pinimg.com/736x/ae/cc/20/aecc20452d1e4a056cc981bae1a8dad8.jpg')">
                <span>North coast sea</span>
            </div>
            <div class="culture-card" style="background-image:url('https://i.pinimg.com/736x/0a/19/44/0a1944cb62dd6f479cba82fa2ce2278c.jpg')">
                <span>Dahab</span>
            </div>
            <div class="culture-card" style="background-image:url('https://i.pinimg.com/736x/4a/cb/ca/4acbca6724ddf96fa99963c7a390e018.jpg')">
                <span>Siwa oasis</span>
            </div>
        </div>
    </section>

    <!-- Blogs Section -->
    <section class="blogs">
        <h2>Our Latest Article</h2>
        <div class="blogs-grid">
            <div class="blog-card">
                <img src="https://i.pinimg.com/736x/e3/96/c9/e396c9f975c8577aac71b87e8a7e98a7.jpg" alt="Sphinx Blog">
                <div class="blog-info">
                    <span class="blog-date">October 1, 2024</span>
                    <h3>Unveiling the Mysteries of the Sphinx</h3>
                </div>
            </div>
            <div class="blog-card">
                <img src="https://i.pinimg.com/736x/99/22/8c/99228cff8e34811ec2e7a92b07cf2e05.jpg" alt="Nile Blog">
                <div class="blog-info">
                    <span class="blog-date">September 20, 2024</span>
                    <h3>Cruising the Nile: A Journey Through Time</h3>
                </div>
            </div>
            <div class="blog-card">
                <img src="https://i.pinimg.com/736x/95/34/c6/9534c6216c3ea4ae1e5cb113454601e8.jpg" alt="Festivals Blog">
                <div class="blog-info">
                    <span class="blog-date">September 12, 2024</span>
                    <h3>Egypt's Festivals<br>Culture and Traditions</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <h2>Our Users Rate Us</h2>
        <div class="testimonials-list">
            <!-- Testimonial 1 -->
            <div class="testimonial">
                <p>"The desert tour was absolutely amazing! The guides were knowledgeable and the experience was unforgettable."<br><span class="testimonial-name">Sarah Johnson</span><br>Desert Adventure, 2023</p>
            </div>
            <!-- Testimonial 2 -->
            <div class="testimonial">
                <p>"Massar made our mountain trek an incredible experience. Everything was well-organized and the views were breathtaking."<br><span class="testimonial-name">Michael Chen</span><br>Mountain Trek, 2023</p>
            </div>
            <!-- Testimonial 3 -->
            <div class="testimonial">
                <p>"The coastal tour was perfect for our family vacation. We'll definitely be booking with Massar again!"<br><span class="testimonial-name">Emily Rodriguez</span><br>Coastal Escape, 2023</p>
            </div>
        </div>
    </section>

        <!-- Services Section -->
        <section class="services">
        <h2>Our Services</h2>
        <div class="services-grid">
            <!-- Service 1 -->
            <div class="service-card">
                <div class="service-icon">
                    <svg width="60" height="60" fill="#cbb279" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
                <h3>Expert Guides</h3>
                <p>Our experienced guides ensure you get the most out of your journey.</p>
            </div>
            <!-- Service 2 -->
            <div class="service-card">
                <div class="service-icon">
                    <svg width="60" height="60" fill="#cbb279" viewBox="0 0 24 24">
                        <path d="M12 21c-4.97 0-9-4.03-9-9s4.03-9 9-9 9 4.03 9 9-4.03 9-9 9zm0-16c-3.87 0-7 3.13-7 7s3.13 7 7 7 7-3.13 7-7-3.13-7-7-7zm1 10h-2v-2h2v2zm0-4h-2V7h2v2z"/>
                    </svg>
                </div>
                <h3>Best Prices</h3>
                <p>We offer competitive prices without compromising on quality.</p>
            </div>
            <!-- Service 3 -->
            <div class="service-card">
                <div class="service-icon">
                    <svg width="60" height="60" fill="#cbb279" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                    </svg>
                </div>
                <h3>Safe Travel</h3>
                <p>Your safety is our top priority on every adventure.</p>
            </div>
            <!-- Service 4 -->
            <div class="service-card">
                <div class="service-icon">
                    <svg width="60" height="60" fill="#cbb279" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>
                <h3>Memorable Experiences</h3>
                <p>Create lasting memories with our carefully curated tours.</p>
            </div>
        </div>
    </section>
</main>

<style>
      /* Reset and base styles */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Roboto', serif;
  background: #f3f1ed;
  color: #222;
  line-height: 1.6;
  font-size: 20px;
  min-height: 100vh;
}

h1, h2, h3, h4 {
  font-family: 'Playfair Display', serif;
  color: #111;
}

h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
  font-weight: bold;
}

h2 {
  font-size: 2.5rem;
  text-align: center;
  margin: 3rem 0 2rem 0;
  font-weight: bold;
}

h3 {
  font-size: 2rem;
  margin: 0.5rem 0;
}

main {
  background: #f3f1ed;
  max-width: 1200px;
  margin: 0 auto;
  box-shadow: 0 0 0 20px #f3f1ed;
  padding-bottom: 2rem;
}
/* Hero Section */
.hero {
    width: 100vw;
    height: 700px;
    background: url('https://i.pinimg.com/736x/89/2e/cb/892ecb329e0ece38e250f6cd140b11db.jpg') center/cover no-repeat;
    margin-bottom: 0;
    position: relative;
    display: flex;
    align-items: flex-end;
    justify-content: flex-start;
}

.hero-overlay {
    position: absolute;
    left: 0;
    bottom: 0;
    padding: 3.5rem 0 3.5rem 3.5rem;
    color: #fff;
    background: linear-gradient(90deg, rgba(30,30,30,0.65) 0%, rgba(30,30,30,0.15) 70%, rgba(30,30,30,0) 100%);
    max-width: 700px;
}

.hero-overlay h1 {
    font-size: 2.8rem;
    color: #fff;
    font-family: 'Playfair Display', serif;
    font-weight: 400;
    margin-bottom: 1.2rem;
    line-height: 1.1;
}

.hero-desc {
    font-size: 1.15rem;
    color: #fff;
    font-family: 'Roboto', serif;
    line-height: 1.5;
    margin-bottom: 0;
    text-shadow: 0 2px 8px rgba(0,0,0,0.25);
}

/* About Us */
.about-us {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 4rem 4vw 2rem 4vw;
    gap: 3rem;
}

.about-text {
    flex: 1.2;
}

.about-text p {
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
    color: #444;
}

.btn-read {
    background: #cbb279;
    color: #fff;
    border: none;
    border-radius: 16px;
    padding: 0.5rem 1.2rem;
    font-size: 1rem;
    cursor: pointer;
    font-family: 'Roboto', serif;
    transition: background 0.2s;
}

.btn-read:hover {
    background: var(--color-gold-dark);
}

.about-img {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.about-img img {
    width: 100%;
    max-width: 400px;
    border-radius: 8px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
}

/* Top Attractions */
.top-attractions {
    margin-top: 2rem;
}

.attractions-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    padding: 0 2vw;
}

.attraction-card {
    background: #fafafa;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 2rem;
    min-height: unset;
}

.attraction-card img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 10px 10px 0 0;
}

.attraction-info {
    display: block;
    padding: 1.2rem 1rem 1.2rem 1rem;
    width: 100%;
    min-width: unset;
    background: #f3f1ed;
    border-radius: 0 0 16px 16px;
    box-sizing: border-box;
    position: relative;
}

.category {
    color: var(--color-gold);
    font-size: 1.8rem;
    font-family: 'Playfair Display', serif;
    font-weight: 400;
    margin-bottom: 0.2rem;
    display: block;
}

.attraction-info h3 {
    font-family: 'Playfair Display', serif;
    font-size: 2.2rem;
    font-weight: 350;
    color: #111;
    margin: 0.2rem 0 1.2rem 0;
}

.btn-explore {
    background: #cbb279;
    color: #fff;
    border: none;
    border-radius: 16px;
    padding: 0.4rem 1.3rem;
    font-size: 1rem;
    float: right;
    margin-top: 0.5rem;
    margin-bottom: 0.9rem;
    font-family: 'Playfair Display', serif;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    transition: background 0.2s;
}

.btn-explore:hover {
    background: var(--color-gold-dark);
}

/* Cultural Experiences */
.cultural-experiences {
    margin-top: 3rem;
}

.culture-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: repeat(3, 220px);
    gap: 1.5rem;
    padding: 0 2vw;
}

.culture-card {
    position: relative;
    background-size: cover;
    background-position: center;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: flex-end;
    min-height: 220px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.culture-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 50%, transparent 100%);
    z-index: 1;
}

.culture-card span {
    position: relative;
    color: #fff;
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem;
    padding: 1.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    z-index: 2;
    transition: all 0.3s ease;
}

/* Hover Effects */
.culture-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.culture-card:hover::before {
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 50%, transparent 100%);
}

.culture-card:hover span {
    transform: translateY(-5px);
    padding-bottom: 2rem;
}

/* Blogs */
.blogs {
    margin-top: 3rem;
}

.blogs-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    padding: 0 2vw;
}

.blog-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    min-height: 520px;
}

.blog-card img {
    width: 100%;
    height: 400px;
    object-fit: cover;
}

.blog-info {
    padding: 1.2rem 1rem 1rem 1rem;
    color: #111;
}

.blog-date {
    color: #888;
    font-size: 1rem;
    display: block;
    margin-bottom: 0.5rem;
}

/* Services */
.services {
    margin-top: 3rem;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
    padding: 0 2vw;
    margin-bottom: 2rem;
}

.service-card {
    background: #f5f5f5;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    padding: 2.5rem 1.5rem 2rem 1.5rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 220px;
}

.service-icon {
    margin-bottom: 1.2rem;
}

.service-card h3 {
    margin-bottom: 0.7rem;
}

.service-card p {
    color: #222;
    font-size: 1.1rem;
}

/* Testimonials */
.testimonials {
    margin-top: 3rem;
}

.testimonials-list {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    padding: 0 2vw 2rem 2vw;
}

.testimonial {
    background: #f3f3f3;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    padding: 2rem 2.5rem;
    font-size: 1.4rem;
    color: #222;
}

.testimonial-name {
    color: var(--color-gold);
    font-size: 1.3rem;
    font-family: 'Playfair Display', serif;
}

/* Footer */
footer {
    background: #f3f1ed;
    padding: 2.5rem 0 0 0;
    margin-top: 2rem;
    border-top: 1px solid #e0e0e0;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2vw;
    gap: 2rem;
}

.footer-brand {
    flex: 1.2;
}

.footer-logo {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    color: var(--color-gold);
    margin-bottom: 0.7rem;
}

.footer-links h4, .footer-contact h4 {
    color: var(--color-gold);
    font-family: 'Playfair Display', serif;
    font-size: 2rem;
    margin-bottom: 0.7rem;
}

.footer-links ul {
    list-style: none;
    margin-top: 0.5rem;
}

.footer-links li {
    margin-bottom: 0.4rem;
    font-size: 1.2rem;
    color: #222;
    font-family: 'Playfair Display', serif;
}

.footer-contact {
    flex: 1.2;
}

.footer-contact p {
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.footer-social {
    display: flex;
    gap: 0.7rem;
    margin-top: 0.5rem;
}

.footer-social a {
    display: inline-block;
    transition: transform 0.2s;
}

.footer-social a:hover {
    transform: scale(1.1);
}

.footer-bottom {
    text-align: center;
    color: #222;
    font-size: 1.3rem;
    font-family: 'Playfair Display', serif;
    margin-top: 2rem;
    padding-bottom: 1.5rem;
}

.footer-categories {
    flex: 1;
    margin-left: 1.5rem;
}

.footer-categories-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem;
    font-weight: bold;
    color: var(--color-gold);
    margin-bottom: 0.7rem;
}

.footer-categories ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-categories li {
    font-family: 'Playfair Display', serif;
    font-size: 1.2rem;
    color: #444;
    margin-bottom: 0.7rem;
    margin-left: 0;
}

/* Responsive Styles */
@media (max-width: 1100px) {
    .about-us {
        flex-direction: column;
        align-items: stretch;
        gap: 2rem;
    }
    .footer-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 2rem;
    }
    .attractions-grid, .blogs-grid, .culture-grid {
        grid-template-columns: 1fr 1fr;
    }
    .services-grid {
        grid-template-columns: 1fr;
    }
    .hero {
        height: 340px;
    }
    .hero-overlay {
        padding: 2rem 0 2rem 1.5rem;
        max-width: 90vw;
    }
}

@media (max-width: 800px) {
    .about-us {
        padding: 2rem 1vw 1rem 1vw;
    }
    .attractions-grid, .blogs-grid, .culture-grid {
        grid-template-columns: 1fr;
    }
    .culture-grid {
        grid-template-rows: repeat(8, 180px);
    }
    .services-grid {
        grid-template-columns: 1fr;
    }
    .testimonials-list {
        padding: 0 1vw 2rem 1vw;
    }
    .footer-content {
        padding: 0 1vw;
    }
    .hero {
        height: 220px;
    }
    .hero-overlay {
        padding: 1rem 0 1rem 0.7rem;
        max-width: 98vw;
    }
}

@media (max-width: 600px) {
    h1 {
        font-size: 2rem;
    }
    h2 {
        font-size: 1.5rem;
    }
    .about-us {
        padding: 1rem 0.5vw 1rem 0.5vw;
    }
    .attraction-card img, .blog-card img {
        height: 180px;
    }
    .culture-card {
        min-height: 120px;
    }
    .testimonial {
        padding: 1rem 0.7rem;
        font-size: 1rem;
    }
    .footer-logo {
        font-size: 1.5rem;
    }
    .footer-bottom {
        font-size: 1rem;
    }
    .hero {
        height: 120px;
    }
    .hero-overlay {
        padding: 0.5rem 0 0.5rem 0.3rem;
    }
    .hero-overlay h1 {
        font-size: 1.1rem;
    }
    .hero-desc {
        font-size: 0.7rem;
    }
}

/* Animation for cards on scroll */
.card-animate {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 0.8s cubic-bezier(0.4,0,0.2,1), transform 0.8s cubic-bezier(0.4,0,0.2,1);
}

.card-animate.visible {
    opacity: 1;
    transform: translateY(0);
}

/* Card hover/click effects */
.attraction-card, .blog-card, .service-card, .culture-card, .testimonial {
    transition: box-shadow 0.3s cubic-bezier(0.4,0,0.2,1), transform 0.3s cubic-bezier(0.4,0,0.2,1);
    cursor: pointer;
}

.attraction-card:hover, .blog-card:hover, .service-card:hover, .culture-card:hover, .testimonial:hover {
    box-shadow: 0 8px 32px rgba(203,178,121,0.18), 0 2px 8px rgba(0,0,0,0.10);
    transform: scale(1.035) translateY(-6px);
    z-index: 2;
}

.attraction-card:active, .blog-card:active, .service-card:active, .culture-card:active, .testimonial:active {
    transform: scale(0.98) translateY(2px);
    box-shadow: 0 2px 8px rgba(203,178,121,0.10), 0 1px 4px rgba(0,0,0,0.08);
}

/* Footer bottom hover */
.footer-bottom {
    transition: background 0.2s, color 0.2s;
}

.footer-bottom:hover {
    background: var(--color-gold);
    color: #fff;
}
</style>

<script>
    // Animate cards on scroll
    function animateCardsOnScroll() {
        const cards = document.querySelectorAll('.attraction-card, .blog-card, .service-card, .culture-card, .testimonial');
        cards.forEach(card => {
            card.classList.add('card-animate');
        });
        function checkCards() {
            cards.forEach(card => {
                const rect = card.getBoundingClientRect();
                if (rect.top < window.innerHeight - 60) {
                    card.classList.add('visible');
                }
            });
        }
        window.addEventListener('scroll', checkCards);
        window.addEventListener('resize', checkCards);
        checkCards();
    }

    // Initialize animations
    window.addEventListener('DOMContentLoaded', () => {
        animateCardsOnScroll();
    });
</script>
@endsection