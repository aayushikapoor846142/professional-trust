

    <div class="slider-container">
        <div class="slider cds-gridview">
        
                @if(!empty($records))
                    @foreach($records as $value)    
                        <div class="inner-card slide">
                            <div class="card-box">
                                <div class="about-companie">
                                    <div class="d-flex d-sm-block d-lg-flex g-2 w-100">
                                        <div class="cds-profile">
                                            <img class="size60 coner" src="{{ $value['image_url'] }}" alt="image">
                                        </div>
                                        <div class="card-text">
                                            <div class="cds-title w-100">
                                                <a href="{{ url('unauthorised-practitioners/'.$value['unique_id'].'/'.str_slug($value['name'])) }}" class="blue-txt">{{ $value['name']}}</a>
                                                <!-- <span class="badge bg-primary">{{$value['uap_type']}}</span> -->
                                                <div class="profile-info ps-sm-2">
                                                    <!-- <span><img src="{{ url('assets/images/icons/bell-on.svg') }}" class="img-fluid" alt="bell"></span> -->
                                                </div>
                                            </div>
                                            <h4 class="mb-1 mt-1 font16 fw-medium cds-title-head">{{$value['owner_name']}}</h4>
                                            <div class="about-work text-color mt-2 mb-2">
                                                <ul class="ms-0">
                                                    @if($value['address'] != '')
                                                        <li><a href="{{ url('unauthorised-practitioners/'.$value['unique_id'].'/'.str_slug($value['name'])) }}" class="link">{{$value['address']}}</a></li>
                                                    @endif
                                                </ul>
                                            </div>                          
                                        </div>
                                    </div>
                                    <div class="cds-level">
                                        <ul class="m-0 d-flex g-2 align-items-center">
                                            @if(!empty($value['uap_level_tags']))
                                                @if(collect($value['uap_level_tags'])->max('level') == 5)
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                @elseif(collect($value['uap_level_tags'])->max('level') == 4)
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
    
                                                @elseif(collect($value['uap_level_tags'])->max('level') == 3)
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                @elseif(collect($value['uap_level_tags'])->max('level') == 2)
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                @elseif(collect($value['uap_level_tags'])->max('level') == 1)
                                                    <li><img src="{{ url('assets/images/icons/info-fill.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                @else
    
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                    <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                @endif
                                            @else
                                                <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                                <li><img src="{{ url('assets/images/icons/info.svg') }}" class="img-fluid" alt="info"></li>
                                            @endif
                                        </ul>
                                        <!-- <div class="level-check"><a href="javascript:;" class="level-link">Level 3</a></div> -->
                                    </div>
                                </div>                  
                            </div>
                            <ul class="about-tag ms-0">
                                @if(!empty($value['uap_level_tags']))
                                    @foreach(collect($value['uap_level_tags'])->where('is_ping',1) as $row)
                                        <li><span class="badge-list link">{{$row['tag_name']}}</span></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>      
                    @endforeach
                @endif
        
        </div>
    </div>
    
<script>
   
        let currentIndex = 0;
        const maxScroll = 5;
        const slides = document.querySelectorAll('.slide');
        const totalSlides = slides.length;
        const slider = document.querySelector('.slider');

        // Clone the first 5 slides and append them to the end of the slider
        const clonedSlides = Array.from(slides).slice(0, maxScroll).map(slide => slide.cloneNode(true));
        clonedSlides.forEach(clone => slider.appendChild(clone));

        // Update the total slides count after cloning
        const updatedTotalSlides = slides.length + clonedSlides.length;

        // Function to auto scroll the slider
        function autoScroll() {
            currentIndex += maxScroll;

            if (currentIndex >= updatedTotalSlides) {
                currentIndex = 0;
                slider.style.transition = 'none';
                slider.style.transform = `translateX(0)`;
                setTimeout(() => {
                    slider.style.transition = 'transform 0.5s ease-in-out';
                }, 50);
            } else {
                slider.style.transform = `translateX(-${(currentIndex * 100) / maxScroll}%)`;
            }
        }

        // Automatically scroll every 3 seconds
        // setInterval(autoScroll, 3000);

        // Manual Scroll Functions
        function nextSlide() {
            if (currentIndex + maxScroll < updatedTotalSlides) {
                currentIndex += maxScroll;
                slider.style.transform = `translateX(-${(currentIndex * 100) / maxScroll}%)`;
            }
        }

        function prevSlide() {
            if (currentIndex - maxScroll >= 0) {
                currentIndex -= maxScroll;
                slider.style.transform = `translateX(-${(currentIndex * 100) / maxScroll}%)`;
            }
        }

        // Button Event Listeners
        document.querySelector('.prev').addEventListener('click', prevSlide);
        document.querySelector('.next').addEventListener('click', nextSlide);
    </script>
