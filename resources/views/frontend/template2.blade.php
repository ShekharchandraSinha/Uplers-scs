<!DOCTYPE html>
<html>

<head>
    <title>{{ $portfolio->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{ asset('frontend/template2/css/style.css') }}">
</head>

<body>
    <div id="main">
        <!-- profile Page START -->
        <section class="profile-pg">
            <div class="container">
                <div class="uplers-logo">
                    <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/uplers-merge-logo.png" />
                </div>
                <div class="row" style="margin: 0">
                    <!-- leftSide START -->
                    <div class="col-sm-4 leftSide">
                        <div class="profilePhoto">
                            <figure>
                                @php
                                if(isset($portfolio->profile_photo)){
                                    $image =$portfolio->profile_photo;
                                } else {

                                    $image = 'dummy/image-placeholder.png';
                                }
                                @endphp
                                <img src="{{ asset('img/portfolio-profile/' . $image) }}" />
                            </figure>
                        </div>
                        <div class="aboutWrap">
                            <div class="about-desc">
                                @if (isset($sectionData['profile']))
                                <p>{{ $sectionData['profile']->model }}}</p>
                                @endif
                                {{-- <ul>
                                    <li>Total Work Experience: <strong>8 Years</strong></li>
                                    <li>Availability: <strong>Full-Time</strong></li>
                                    <li>Joining Period: <strong>2-4 weeks</strong></li>
                                    <li>Preferable Working Hours: <strong>US Shift</strong></li>
                                    <li>Type of Developer: <strong>WordPress Backend Developer</strong></li>
                                </ul> --}}
                            </div>
                            {{-- <div class="skillList">
                                <h4 class="left-sec-title">Domain expertise/ PROFICIENCY</h4>
                                <ul>
                                    <li><span class="name">WordPress Page/Theme Build</span><span class="years">7 Years</span></li>
                                    <li><span class="name">Git/SVN</span><span class="years">2 Years</span></li>
                                    <li><span class="name">PHP</span><span class="years">2 Years</span></li>
                                    <li><span class="name">MySQL</span><span class="years">7 Years</span></li>
                                    <li><span class="name">Laravel</span><span class="years">2 Years</span></li>
                                    <li><span class="name">React</span><span class="years">2 Years</span></li>
                                    <li><span class="name">Angular</span><span class="years">2 Years</span></li>
                                    <li><span class="name">Vue</span><span class="years">7 Years</span></li>
                                    <li><span class="name">Ajax</span><span class="years">2 Years</span></li>
                                    <li><span class="name">Gulp.js</span><span class="years">2 Years</span></li>
                                </ul>
                            </div> --}}

                            @if(isset($sectionData['testimonial']) && $sectionData['testimonial']->display)
                            <div class="feedback-qoute">
                                <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/qoute-icon.png" />
                                <p>{{ $sectionData['testimonial']->model }}</p>
                                {{-- <ul>
                                        <li>API/Codex Knowledge</li>
                                        <li>Theme building</li>
                                        <li>WP Coding Standards</li>
                                        <li>React</li>
                                        <li>Communication</li>
                                        <li>Project Management</li>
                                        <li>Team Player</li>
                                    </ul> --}}
                            </div>
                            @endif
                            @if(isset($sectionData['clientele']) && $sectionData['clientele']->display)
                                <div class="client-ele">
                                    <h4 class="left-sec-title">CLIENTELE</h4>
                                    <ul>
                                        <li>
                                            <h5>{{ $sectionData['clientele']->model->metric->count }}+</h5>
                                            <p>{{ $sectionData['clientele']->model->metric->title }}</p>
                                        </li>
                                        <li>
                                            <h5>{{ $sectionData['clientele']->model->agencies }}</h5>
                                            <p>Agencies Served</p>
                                        </li>
                                        <li>
                                            <h5>{{ $sectionData['clientele']->model->agencies }}</h5>
                                            <p>Companies from {{ strtoupper(join(', ', $sectionData['clientele']->model->countries)) }}</p>
                                        </li>
                                        <li>
                                            <h5>
                                                <div class="rating-star-container">
                                                    <div class="active-stars" style="width: {{ $sectionData['clientele']->model->overallRating * 30 }}px"></div>
                                                </div>
                                            </h5>
                                            <p>Overall Project Rating</p>
                                        </li>
                                        <li>
                                            <h5>
                                                <div class="rating-star-container">
                                                    <div class="active-stars" style="width: {{ $sectionData['clientele']->model->clientSatisfaction * 30 }}px"></div>
                                                </div>
                                            </h5>
                                            <p>Client Satisfaction</p>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                            @if(isset($sectionData['hobbies']) && $sectionData['hobbies']->display && sizeof($sectionData['hobbies']->model) > 0)
                            <div class="interests">
                                <h4 class="left-sec-title">INTERESTS</h4>
                                @foreach ($sectionData['hobbies']->model as $hobby)
                                <div class="intBox">
                                    <span>{{ $hobby->title }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    <!-- leftSide END -->
                    <div class="col-sm-8 rightSide">
                        <div class="main-title">
                            <h1><span class="underline">{{ $portfolio->name }}</span></h1>
                            <h4 class="right-sec-title">{{ $portfolio->designation }}</h4>
                            <ul>
                                @if(isset($sectionData['experience']) && $sectionData['experience']->display)
                                    @foreach ($sectionData['experience']->model as $model)
                                    <li>
                                        <p><strong>{{ $model->title }}</strong> &nbsp;&nbsp;<small>{{ $model->company }} &nbsp;|&nbsp; {{ $model->start }} - {{ $model->end }}</small></p>
                                        <ul class="dashed">
                                            @foreach ($model->content as $content)
                                            <li>{{ $content->model }}</li>
                                            @endforeach
                                        </ul>
                                        {{-- <div class="tags">
                                            <span>PHP</span>
                                            <span>HTML5</span>
                                            <span>CSS3</span>
                                            <span>JavaScript</span>
                                        </div> --}}
                                    </li>
                                    @endforeach
                                @endif
                            </ul>
                            {{-- <h4 class="right-sec-title">MAJOR PROJECTS</h4>
                            <div class="row major-projects">
                                <div class="col-sm-12 proBox">
                                    <figure>
                                        <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/simple-sales.png" />
                                    </figure>
                                    <div class="info">
                                        <h5>Simple Sales Tax Plugin<small> Amazon Web Services | Aug, 2019 - Oct, 2019</small></h5>
                                        <p>(Development) Technologies: PHP, JavaScript, HTML, CSS <br />Aug, 2019 - Oct, 2019</p>
                                        <div class="tags">
                                            <span>PHP</span>
                                            <span>HTML5</span>
                                            <span>CSS3</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 proBox">
                                    <figure>
                                        <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/sky-id.png" />
                                    </figure>
                                    <div class="info">
                                        <h5>Skyld<small> HPE Hybrid Cloud Solutions | Aug, 2019 - Oct, 2019</small></h5>
                                        <p>
                                            (Development) Technologies: PHP, HTML, CSS, JavaScript <br />
                                            Oct, 2019 - Sep, 2020
                                        </p>
                                        <div class="tags">
                                            <span>Project Management</span>
                                            <span>HTML5</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 proBox">
                                    <figure>
                                        <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/firetools.png" />
                                    </figure>
                                    <div class="info">
                                        <h5>Wildlife Workshop Firetools<small> Emtec Business & Technology | Aug, 2019 - Oct, 2019</small></h5>
                                        <p>(Development) <br />Oct, 2020 - Nov, 2020</p>
                                        <div class="tags">
                                            <span>Team Handling</span>
                                            <span>CSS3</span>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            @if (isset($sectionData['achievements']) && $sectionData['achievements']->display)
                            <h4 class="right-sec-title">TOP {{ sizeof($sectionData['achievements']->model) }} ACHIEVEMENTS</h4>
                            <ul class="star-list">
                                @foreach ($sectionData['achievements']->model as $content)
                                <li>{{ $content->model }}</li>
                                @endforeach
                            </ul>
                            @endif
                            @if ( ($esps->count() > 0 && $sectionData['esp']->display)  || ($pms->count() > 0 && $sectionData['pms']->display) || ($frameworks->count() > 0 && $sectionData['frameworks']->display))    
                                <h4 class="right-sec-title">APPLICATIONS & TOOLS KNOWN</h4>
                                <ul class="logos">
                                    @if ($esps->count() > 0 && $sectionData['esp']->display)
                                        @foreach ($esps as $management)
                                        <li><img src="{{ asset('img/esp/'.$management->icon) }}" /></li>
                                        @endforeach
                                    @endif
                                    @if ($pms->count() > 0 && $sectionData['pms']->display)
                                        @foreach ($pms as $management)
                                        <li><img src="{{ asset('img/pms/'.$management->icon) }}" /></li>
                                        @endforeach
                                    @endif
                                    @if ($frameworks->count() > 0 && $sectionData['frameworks']->display)
                                        @foreach ($frameworks as $management)
                                        <li><img src="{{ asset('img/framework/'.$management->icon) }}" /></li>
                                        @endforeach
                                    @endif
                                </ul>
                            @endif
                            {{-- <h4 class="right-sec-title">SKILLS & COMPETENCIES</h4>
                            <ul>
                                <li>
                                    <p><strong>WordPress Capabilities</strong></p>
                                    <p>API/Codex Knowledge, WP Coding Standards, Custom Post Types/Custom Meta Fields, Woocommerce, Shortcodes, Templating, Multisite, Multilingual websites, Theme building, Security</p>
                                </li>
                                <li>
                                    <p><strong>Core Backend & Server Side Expertise</strong></p>
                                    <ul class="rate-list">
                                        <li>Angular <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/4-star.png" /></li>
                                        <li>Drupal <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/4-star.png" /></li>
                                        <li>React <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/4-star.png" /></li>
                                        <li>Big Commerce <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/4-star.png" /></li>
                                        <li>Vue <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/4-star.png" /></li>
                                        <li>Wix <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/4-star.png" /></li>
                                        <li>Laravel <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/4-star.png" /></li>
                                        <li>Shopify <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/4-star.png" /></li>
                                        <li>Magento <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/4-star.png" /></li>
                                        <li>Cloud Server (Google/AWS) <img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/4-star.png" /></li>
                                    </ul>
                                </li>
                                <li>
                                    <p><strong>Client Communication & Project Handling</strong></p>
                                    <p>Project management, account management, quality assurance</p>
                                </li>
                            </ul> --}}
                            @if(isset($sectionData['qualities']) && $sectionData['qualities']->display && sizeof($sectionData['qualities']->model) > 0)
                            <h4 class="right-sec-title">plus points/ key qualities</h4>
                            <ul class="key-quality">
                                @foreach ($sectionData['qualities']->model as $item)
                                <li>{{ $item->title }}</li>
                                @endforeach
                            </ul>
                            @endif
                            @if(isset($sectionData['education']))
                            <h4 class="right-sec-title">EDUCATION</h4>
                            <ul>
                                @foreach ($sectionData['education']->model as $model)
                                <li>
                                    <p><strong>{{ $model->course }}</strong> {{ $model->start }} - {{ $model->end }}</p>
                                    @if ($model->content != "")
                                    <ul>
                                        <li>{{ $model->content }}</li>
                                    </ul>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                            @endif
                            {{-- <h4 class="right-sec-title">CERTIFICATIONS</h4>
                            <ul class="logos">
                                <li><img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/cr-angular-spring-icon.jpg" />Angular & Spring Boot</li>
                                <li><img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/cr-javascript-icon.jpg" />JavaScript</li>
                                <li><img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/cr-laravel-icon.jpg" />Laravel</li>
                                <li><img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/cr-react-icon.jpg" />React</li>
                                <li><img src="https://www.uplers.com/wp-content/themes/uplers/assets/images/profile/cr-vue-icon.jpg" />Vue</li>
                            </ul> --}}
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- profile Page END -->
    </div>
</body>

</html>