@extends('layouts.clean')
@section('title', 'A calmer classroom')

@section('body')
<div class="landing">
    <header class="landing-nav">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark" aria-hidden="true">ME</span>
            <span class="brand-name">Classroom</span>
        </a>
        <nav class="d-flex gap-2" aria-label="Account">
            <a href="{{ route('login') }}" class="btn btn-ghost">Sign in</a>
            <a href="{{ route('signup') }}" class="btn btn-primary">Get started</a>
        </nav>
    </header>

    <main id="main" class="landing-hero">
        <div>
            <p class="eyebrow">For teachers and students</p>
            <h1>Everything your class needs, <em>in one tidy place.</em></h1>
            <p class="lead">
                Share materials, post questions, collect answers and give grades — then keep the
                conversation going in a chat that belongs to the class.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('signup') }}" class="btn btn-primary btn-lg">Create a free account</a>
                <a href="{{ route('login') }}" class="btn btn-quiet btn-lg">I already have one</a>
            </div>

            <ul class="feature-list">
                <li><i class="fa-solid fa-check" aria-hidden="true"></i><span><strong>Join with a code.</strong> Students enter a short class code — no invitations to chase.</span></li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i><span><strong>Questions with due dates.</strong> Students turn work in; you grade it on one page.</span></li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i><span><strong>Class chat.</strong> Real-time messages visible only to members of the class.</span></li>
            </ul>
        </div>

        {{-- Illustrative preview built from the app's own components --}}
        <div class="preview-stack" aria-hidden="true">
            <div class="preview-card">
                <div class="class-banner theme-0">
                    <span class="class-name" style="pointer-events:none">Grade 10 Biology</span>
                    <div class="class-section">Section B · Mornings</div>
                </div>
                <div class="feed-item is-question">
                    <span class="feed-icon"><i class="fa-solid fa-clipboard-question"></i></span>
                    <span class="feed-main"><p class="feed-title">Cell structure review</p><p class="feed-meta">5 questions · 20 points</p></span>
                    <span class="feed-aside">Due Fri</span>
                </div>
                <div class="feed-item">
                    <span class="feed-icon"><i class="fa-solid fa-book-open"></i></span>
                    <span class="feed-main"><p class="feed-title">Week 4 slides</p><p class="feed-meta">PDF attached</p></span>
                    <span class="feed-aside">Mon</span>
                </div>
            </div>
            <div class="preview-card">
                <div class="panel-body d-flex align-items-center gap-3">
                    <span class="avatar avatar-sm" style="background: var(--clay-soft); color: var(--clay)">A</span>
                    <div class="flex-fill">
                        <div class="fw-medium">Ava turned in “Cell structure review”</div>
                        <div class="small text-muted-2">Graded 18 / 20</div>
                    </div>
                    <span class="pill pill-accent">Graded</span>
                </div>
            </div>
        </div>
    </main>

    <footer class="landing-footer">
        &copy; {{ date('Y') }} {{ config('app.name', 'ME Classroom') }}
    </footer>
</div>
@endsection
