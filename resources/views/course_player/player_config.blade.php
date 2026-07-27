@push('js')
    <script>
        "use strict";
        // Use window.videoPlayer to avoid conflicts with other 'player' variables
        if (typeof window.videoPlayer === 'undefined') {
            window.videoPlayer = null;
        }

        // Initialize Plyr only if #player element exists (for HTML5/YouTube videos)
        if (document.querySelector('#player')) {
            console.log('Initializing Plyr player...');
            window.videoPlayer = new Plyr('#player', {
                youtube: {
                    // Options for YouTube player
                    controls: 1, // Show YouTube controls
                    modestBranding: false, // Show YouTube logo
                    showinfo: 1, // Show video title and uploader on play
                    rel: 0, // Show related videos at the end
                    iv_load_policy: 3, // Do not show video annotations
                    cc_load_policy: 1, // Show captions by default
                    autoplay: false, // Do not autoplay
                    loop: false, // Do not loop the video
                    mute: false, // Do not mute the video
                    start: 0, // Start at this time (in seconds)
                    end: null // End at this time (in seconds)
                }
            });

            // Wait for Plyr to be ready
            window.videoPlayer.on('ready', function() {
                console.log('Plyr player is ready!');
                console.log('Player type:', window.videoPlayer.provider);
                console.log('Player source:', window.videoPlayer.source);
            });
        } else {
            console.log('No #player element found');
        }
    </script>
@endpush

<style type="text/css">
    .plyr__progress video {
        width: 180px !important;
        height: auto !important;
        position: absolute !important;
        bottom: 30px !important;
        z-index: 1 !important;
        border-radius: 10px !important;
        border: 2px solid #fff !important;
        display: none;
        background-color: #000;
    }

    .plyr__progress video:hover {
        display: none !important;
    }

    video:not(.plyr:fullscreen video) {
        width: 100%;
        max-height: auto !important;
        max-height: 567px !important;
        border-radius: 5px;
    }

    /* Overlay and progress bar styling */
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        visibility: hidden;
    }

    /* Circular progress bar container */
    .circular-progress-container {
        position: relative;
        width: 100px;
        height:100px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Outer circle border (for border effect) */
    .outer-circle {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        stroke: #ddd; /* Border color */
        stroke-width: 7;
        fill: none;
    }

    /* Inner circle for progress animation */
    .circular-progress {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        stroke-dasharray: 440; /* Circumference of the circle */
        stroke-dashoffset: 440;
        stroke: #6610f2; /* Progress color */
        stroke-width: 7;
        fill: none;
        transition: stroke-dashoffset 5s linear;
    }

    .progress-ring {
        transform: rotate(-90deg); /* To start progress from the top */
    }

    .cancel-icon {
        position: absolute;
        top: 6px;
        right: 6px;
        cursor: pointer;
        background: #ff0000;
        color: #fff;
        font-size: 18px;
        height: 30px;
        width: 30px;
        line-height: 32px;
        border-radius: 50%;
        text-align: center;
    }
    .overlay-text {
        position: absolute;
        font-size: 16px;
        color: #ffffff;
        text-align: center;
        top: 70%;
        transform: translateY(-50%);
    }
</style>

<div class="overlay" id="nextVideoOverlay">
    <div class="circular-progress-container">
        <svg class="progress-ring" width="100" height="100">
            <!-- Outer Circle (border) -->
            <circle class="outer-circle" cx="50" cy="50" r="45" />
            <!-- Inner Circle (progress) -->
            <circle class="circular-progress" cx="50" cy="50" r="45" />
        </svg>
    </div>
    <div class="overlay-text">Playing next video in <span id="countdown">5</span> sec</div>
    <div class="cancel-icon" id="cancelNextVideo">✖</div>
</div>

<script type="text/javascript">
    // Define elements for overlay and countdown
    const overlay = document.getElementById('nextVideoOverlay');
    const countdownElement = document.getElementById('countdown');
    const cancelNextVideoButton = document.getElementById('cancelNextVideo');
    let countdownInterval;

    // Function to start countdown
    function startCountdown() {
        let countdown = 5; // Countdown set to 5 seconds
        countdownElement.textContent = countdown;
        overlay.style.visibility = 'visible';

        // Restart the circular progress animation
        const circleProgress = document.querySelector('.circular-progress');
        circleProgress.style.transition = 'none'; // Remove previous transition
        circleProgress.style.strokeDashoffset = 440; // Reset stroke offset
        setTimeout(() => {
            circleProgress.style.transition = 'stroke-dashoffset 5s linear';
            circleProgress.style.strokeDashoffset = 0; // Animate the circle fill to complete
        }, 10);

        countdownInterval = setInterval(() => {
            countdown -= 1;
            countdownElement.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(countdownInterval);
                overlay.style.visibility = 'hidden';

                let lesson_id = '{{ $lesson_details['id'] }}';
                let course_id = '{{ $course_details['id'] }}';
                var next_lesson_id = '{{ next_lesson($course_details['id'], $lesson_details['id']) }}';

                if (next_lesson_id) {
                    const url = '{{ url("play-course") }}' + '/' + '{{ Str::slug($course_details['title']) }}' + '-' + course_id + '/' + next_lesson_id;
                    window.location.href = url; // Redirect to the next lesson
                }
            }

        }, 1000);
    }

    // Event listener for video end
    if (typeof window.videoPlayer === 'object' && window.videoPlayer !== null) {
        window.videoPlayer.addEventListener('ended', () => {
            console.log('Video has ended');
            var next_lesson_id = '{{ next_lesson($course_details['id'], $lesson_details['id']) }}';
            if (next_lesson_id) {
                startCountdown(); // Start showing countdown when video ends
            }
        });
    }

    // Cancel next video if user clicks cancel icon
    cancelNextVideoButton.addEventListener('click', () => {
        clearInterval(countdownInterval);
        overlay.style.visibility = 'hidden';
        console.log('Next video playback canceled');
    });
</script>


<!-- Update Watch history and set current duration-->
<script type="text/javascript">
    let lesson_id = '{{ $lesson_details['id'] }}';
    let course_id = '{{ $course_details['id'] }}';
    var currentProgress = '{{ lesson_progress($lesson_details['id']) }}';
    let previousSavedDuration = 0;
    let currentDuration = 0;

    console.log('Initializing watch history tracking...');
    console.log('Lesson ID:', lesson_id);
    console.log('Course ID:', course_id);

    // Get watched duration first
    var watched_duration = @json(get_watched_duration($lesson_details['id'], auth()->user()->id));
    watched_duration = JSON.parse(watched_duration);
    var previous_duration = watched_duration && watched_duration.current_duration > 0
        ? watched_duration.current_duration
        : 0;

    console.log('Previous duration:', previous_duration);

    // Track if we've already unlocked for this lesson
    let hasUnlockedNextLesson = false;

    // Function to unlock next lesson
    function unlockNextLesson(isCompleted) {
        if (isCompleted == 1 && !hasUnlockedNextLesson) {
            hasUnlockedNextLesson = true; // Prevent multiple unlocks
            console.log('Lesson completed! Unlocking next lesson...');

            // Find current lesson item
            let currentLessonItem = document.querySelector('.coourse-playlist-item.active');

            if (currentLessonItem) {
                // Mark current lesson as completed
                let currentCheckIcon = currentLessonItem.querySelector('.me-auto');
                if (currentCheckIcon) {
                    currentCheckIcon.innerHTML = '<i class="fas fa-check-circle checkbox-icon" title="Lesson completed"></i>';
                }
            }

            // Find all lesson items in order
            let allLessonItems = document.querySelectorAll('.coourse-playlist-item');
            let currentLessonIndex = -1;

            // Find current lesson index
            for (let i = 0; i < allLessonItems.length; i++) {
                if (allLessonItems[i].classList.contains('active')) {
                    currentLessonIndex = i;
                    break;
                }
            }

            // Find the FIRST locked lesson after the current one
            let nextLockedLesson = null;
            if (currentLessonIndex !== -1) {
                for (let i = currentLessonIndex + 1; i < allLessonItems.length; i++) {
                    let lockIcon = allLessonItems[i].querySelector('.fa-lock');
                    if (lockIcon) {
                        nextLockedLesson = allLessonItems[i];
                        break; // ✅ نوقف عند أول درس مقفل فقط
                    }
                }
            }

            // If no locked lesson found after current (last lesson in course)
            // Don't unlock anything
            if (nextLockedLesson) {
                // Replace lock icon with play icon
                let lockIcon = nextLockedLesson.querySelector('.fa-lock');
                if (lockIcon && lockIcon.parentElement) {
                    lockIcon.parentElement.innerHTML = '<i class="form-check-input flexCheckChecked mt-0" title="Play Now"></i>';
                }

                // Enable the link
                let lessonLink = nextLockedLesson.querySelector('a');
                if (lessonLink) {
                    lessonLink.style.pointerEvents = 'auto';
                    lessonLink.style.opacity = '1';
                }

                console.log('Next lesson unlocked successfully!');

                // Add a visual effect
                nextLockedLesson.style.transition = 'all 0.3s ease';
                nextLockedLesson.style.backgroundColor = '#d4edda';

                // Optional: Show a success message
                // alert('تم فتح الدرس التالي!');

                setTimeout(function() {
                    nextLockedLesson.style.backgroundColor = '';
                }, 2000);
            } else {
                console.log('No more locked lessons - Course completed or last lesson!');
            }
        }
    }

    // Handle regular video players (HTML5, YouTube via Plyr)
    // Wait for player to be ready before attaching events
    function initializePlayerTracking() {
        if (typeof window.videoPlayer === 'object' && window.videoPlayer !== null) {
            console.log('Regular video player detected (HTML5/YouTube)');
            console.log('Player object:', window.videoPlayer);

            let trackingInterval = null;
            let hasStartedPlaying = false;
            let isCurrentlyPlaying = false;

            // Start tracking only when video starts playing
            window.videoPlayer.on('playing', function() {
                isCurrentlyPlaying = true;

                if (!hasStartedPlaying) {
                    hasStartedPlaying = true;
                    console.log('Video started playing - Starting watch history tracking');
                }

                // Start interval if not already running
                if (!trackingInterval) {
                    console.log('Starting tracking interval...');
                    trackingInterval = setInterval(function() {
                        // Check if video is playing using our flag or Plyr's property
                        let isPlaying = isCurrentlyPlaying || (window.videoPlayer.playing !== undefined ? window.videoPlayer.playing : true);

                        if (isPlaying) {
                            currentDuration = parseInt(window.videoPlayer.currentTime) ?? 5;

                            console.log('Current duration:', currentDuration, 'Playing:', isPlaying);

                            if (lesson_id && course_id && (currentDuration % 5) == 0 && previousSavedDuration != currentDuration) {
                                previousSavedDuration = currentDuration;
                                let url = "{{ route('update_watch_history') }}";

                                console.log('Updating watch history for regular player:', currentDuration);

                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    data: {
                                        lesson_id: lesson_id,
                                        course_id: course_id,
                                        current_duration: currentDuration,
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // Add CSRF token from meta tag
                                    },
                                    timeout: 10000, // 10 seconds timeout
                                    success: function(response) {
                                        console.log('Watch history updated successfully:', response);
                                        console.log('Course progress:', response.course_progress);
                                        console.log('Is completed:', response.is_completed);

                                        // Unlock next lesson if current lesson is completed
                                        if (response.is_completed == 1) {
                                            unlockNextLesson(response.is_completed);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error updating watch history:', error);
                                        console.error('Status:', status);
                                        console.error('Response:', xhr.responseText);
                                        console.error('Status Code:', xhr.status);
                                    }
                                });
                            }
                        }
                    }, 900);
                }
            });

            // Pause tracking when video is paused
            window.videoPlayer.on('pause', function() {
                isCurrentlyPlaying = false;
                console.log('Video paused');
            });

            // Also listen to play event
            window.videoPlayer.on('play', function() {
                isCurrentlyPlaying = true;
                console.log('Video play event fired');
            });

            // Listen to timeupdate event as fallback
            window.videoPlayer.on('timeupdate', function() {
                if (!trackingInterval && isCurrentlyPlaying) {
                    console.log('Timeupdate event - current time:', window.videoPlayer.currentTime);
                }
            });
        } else {
            console.log('No video player found, retrying in 500ms...');
            setTimeout(initializePlayerTracking, 500);
        }
    }

    // Try to initialize immediately
    if (typeof window.videoPlayer !== 'undefined' && window.videoPlayer !== null) {
        initializePlayerTracking();
    } else {
        // Wait for player to be initialized
        setTimeout(initializePlayerTracking, 1000);
    }

    // Handle Google Drive iframe videos
    if (document.getElementById('google-drive-player')) {
        let googleDriveTimer = null;
        let googleDriveStartTime = Date.now();
        let googleDriveCurrentDuration = previous_duration || 0;
        let isTracking = false;
        let lastUpdateTime = Date.now();
        let trackingStarted = false;

        // Function to start tracking
        function startGoogleDriveTracking() {
            if (isTracking || trackingStarted) return;

            trackingStarted = true;
            isTracking = true;
            googleDriveStartTime = Date.now();
            console.log('Google Drive tracking started at duration:', googleDriveCurrentDuration);

            // Update duration every second
            googleDriveTimer = setInterval(function() {
                if (!isTracking) return;

                // Calculate elapsed time since tracking started
                let elapsedSeconds = Math.floor((Date.now() - googleDriveStartTime) / 1000);
                googleDriveCurrentDuration = previous_duration + elapsedSeconds;

                // Update watch history every 5 seconds
                if (lesson_id && course_id && googleDriveCurrentDuration > 0) {
                    let timeSinceLastUpdate = Math.floor((Date.now() - lastUpdateTime) / 1000);

                    if (timeSinceLastUpdate >= 5 && (googleDriveCurrentDuration % 5) == 0 && previousSavedDuration != googleDriveCurrentDuration) {
                        previousSavedDuration = googleDriveCurrentDuration;
                        lastUpdateTime = Date.now();
                        let url = "{{ route('update_watch_history') }}";

                        console.log('Updating Google Drive watch history:', googleDriveCurrentDuration);

                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                lesson_id: lesson_id,
                                course_id: course_id,
                                current_duration: googleDriveCurrentDuration,
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            timeout: 10000, // 10 seconds timeout
                            success: function(response) {
                                console.log('Google Drive watch history updated:', response);
                                console.log('Course progress:', response.course_progress);
                                console.log('Is completed:', response.is_completed);

                                // Unlock next lesson if current lesson is completed
                                if (response.is_completed == 1) {
                                    unlockNextLesson(response.is_completed);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error updating Google Drive watch history:', error);
                                console.error('Status:', status);
                                console.error('Response:', xhr.responseText);
                                console.error('Status Code:', xhr.status);
                            }
                        });
                    }
                }
            }, 1000); // Check every second
        }

        // Start tracking when iframe loads
        let googleDriveIframe = document.getElementById('google-drive-player');
        if (googleDriveIframe) {
            // Try to start immediately if iframe is already loaded
            if (googleDriveIframe.contentDocument || googleDriveIframe.contentWindow) {
                console.log('Google Drive iframe already loaded');
                setTimeout(function() {
                    startGoogleDriveTracking();
                }, 1000);
            }

            // Also listen for load event
            googleDriveIframe.addEventListener('load', function() {
                console.log('Google Drive iframe load event fired');
                setTimeout(function() {
                    startGoogleDriveTracking();
                }, 1000);
            });

            // Start on any user interaction with the page
            let interactionEvents = ['click', 'touchstart', 'keydown', 'mousemove'];
            interactionEvents.forEach(function(eventType) {
                document.addEventListener(eventType, function() {
                    if (!trackingStarted) {
                        console.log('Starting tracking on user interaction:', eventType);
                        startGoogleDriveTracking();
                    }
                }, { once: true, passive: true });
            });

            // Start tracking after page is fully loaded (fallback)
            if (document.readyState === 'complete') {
                setTimeout(function() {
                    startGoogleDriveTracking();
                }, 2000);
            } else {
                window.addEventListener('load', function() {
                    setTimeout(function() {
                        startGoogleDriveTracking();
                    }, 2000);
                });
            }

            // Also try to start on DOMContentLoaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(function() {
                        startGoogleDriveTracking();
                    }, 2000);
                });
            }

            // Force start after 3 seconds as absolute fallback
            setTimeout(function() {
                if (!trackingStarted) {
                    console.log('Force starting Google Drive tracking (fallback)');
                    startGoogleDriveTracking();
                }
            }, 3000);
        }

        // Stop tracking when user leaves the page
        window.addEventListener('beforeunload', function() {
            if (googleDriveTimer) {
                clearInterval(googleDriveTimer);
            }
            // Send final update using sendBeacon
            if (isTracking && lesson_id && course_id && googleDriveCurrentDuration > 0) {
                let url = "{{ route('update_watch_history') }}";
                let formData = new FormData();
                formData.append('lesson_id', lesson_id);
                formData.append('course_id', course_id);
                formData.append('current_duration', googleDriveCurrentDuration);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                // Use sendBeacon for reliable delivery on page unload
                if (navigator.sendBeacon) {
                    navigator.sendBeacon(url, formData);
                }
            }
        });

        // Visibility change handler (when user switches tabs)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                console.log('Page hidden - pausing tracking');
                // Don't stop tracking, just log it
            } else {
                console.log('Page visible - ensuring tracking is active');
                if (!trackingStarted) {
                    startGoogleDriveTracking();
                }
            }
        });
    }

    // Set previous time for regular video players (HTML5, YouTube)
    if (typeof window.videoPlayer === 'object' && window.videoPlayer !== null) {
        var previousTimeSetter = setInterval(function() {
            if (window.videoPlayer.playing == false && window.videoPlayer.currentTime != previous_duration) {
                window.videoPlayer.currentTime = previous_duration;
                // console.log(previous_duration);
                // console.log(player.currentTime);
            } else {
                clearInterval(previousTimeSetter);
            }
        }, 200);
    }

</script>
