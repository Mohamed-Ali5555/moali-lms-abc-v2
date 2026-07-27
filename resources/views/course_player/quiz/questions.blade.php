<style>
.option-image {
    max-width: 100%;
    max-height: 120px;
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 8px;
    transition: transform 0.2s, box-shadow 0.2s;
    display: block;
    margin: 0 auto;
}

@media (max-width: 767px) {
    .option-image {
        max-height: 160px;
    }
}

.option-image:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.quiz-image-frame {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 8px;
    gap: 8px;
}

.quiz-image-frame .quiz-image-preview {
    width: auto;
    max-width: 100%;
    height: auto;
    max-height: 300px;
    object-fit: contain;
    display: block;
    border-radius: 8px;
    cursor: pointer;
}

@media (max-width: 767px) {
    .quiz-image-frame .quiz-image-preview {
        max-height: 240px;
    }
}

body[data-theme="dark"] .quiz-image-frame {
    background: #111727;
}

.question-image-actions {
    display: flex;
    justify-content: center;
    width: 100%;
}

.question-title-content {
    flex: 1 1 auto;
    min-width: 0;
    width: 100%;
}

.question-title-content img,
.question-rich-image {
    width: auto !important;
    height: auto !important;
    max-width: 100% !important;
    max-height: 320px !important;
    object-fit: contain !important;
    display: block;
    margin: 0.5rem auto;
    border-radius: 8px;
    cursor: pointer;
}

.quiz-inline-image-scroll {
    width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    text-align: center;
    padding: 4px 0;
    margin: 0.5rem 0;
}

.quiz-inline-image-scroll img {
    width: auto !important;
    max-width: none !important;
    max-height: 280px !important;
    height: auto !important;
    object-fit: contain !important;
    display: inline-block;
    margin: 0 auto;
    cursor: pointer;
}

@media (max-width: 767px) {
    .question-title {
        flex-direction: column;
        align-items: stretch;
    }

    .question-title .serial {
        align-self: flex-start;
    }

    .question-title-content img,
    .question-rich-image {
        max-height: 260px !important;
    }

    .quiz-inline-image-scroll img {
        max-height: 240px !important;
    }
}

.answer-container {
    padding: 12px;
    border-radius: 8px;
    border: 2px solid #ddd;
    background: #fafafa;
    transition: all 0.3s;
    cursor: pointer;
    min-height: 100px;
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
}
/* dark secction  */
body[data-theme="dark"] .answer-container {
       background:#111727;
}
body[data-theme="dark"] .answer-container.selected {
       background:#011b61;
}
/* end dark section */
.answer-container:hover {
    background: #f0f4ff;
    border-color: #4a90e2;
}

.answer-container.selected {
    background: #e8f4fd;
    border-color: #4a90e2;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.2);
}

/* Custom checkbox square */
.custom-checkbox {
    width: 24px;
    height: 24px;
    border: 2px solid #4a90e2;
    border-radius: 4px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s;
    position: relative;
}

/* Radio button style (circle) */
.answer-container:has(input[type="radio"]) .custom-checkbox {
    border-radius: 50%;
}

.answer-container.selected .custom-checkbox {
    background: #4a90e2;
    border-color: #4a90e2;
}

/* Checkmark for checkbox */
.custom-checkbox::after {
    content: '';
    width: 6px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
    opacity: 0;
    transition: opacity 0.2s;
}

.answer-container.selected .custom-checkbox::after {
    opacity: 1;
}

/* Dot for radio button */
.answer-container:has(input[type="radio"]) .custom-checkbox::after {
    width: 8px;
    height: 8px;
    border: none;
    background: white;
    border-radius: 50%;
    transform: none;
}

/* Hide default checkbox and radio */
.answer-container input[type="checkbox"],
.answer-container input[type="radio"] {
    display: none;
}

.option-content {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 80px;
    padding: 8px;
}

.view-image-btn {
    padding: 6px 12px;
    border-radius: 6px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.3s;
    font-size: 12px;
    font-weight: 500;
    gap: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.view-image-btn:hover {
    background: linear-gradient(135deg, #5568d3 0%, #653a8f 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.view-image-btn:active {
    transform: translateY(0);
}

.view-image-btn i {
    font-size: 14px;
}

/* Image Modal Styles */
#imageModal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.85);
    z-index: 9999;
    overflow: auto;
    display: none;
    padding: 16px;
    box-sizing: border-box;
}

#imageModal .modal-content {
    max-width: min(96vw, 1200px);
    max-height: 90vh;
    margin: 20px auto;
    position: relative;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 767px) {
    #imageModal {
        padding: 8px;
    }

    #imageModal .modal-content {
        max-width: 96vw;
        max-height: 92vh;
        margin: 10px auto;
        padding: 12px;
    }
}

#imageModal .modal-content > span {
    position: absolute;
    top: 15px;
    right: 20px;
    cursor: pointer;
    font-size: 28px;
    color: #666;
    font-weight: bold;
    z-index: 10000;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #f0f0f0;
    transition: all 0.2s;
}

#imageModal .modal-content > span:hover {
    background: #e0e0e0;
    color: #333;
}

#modalImage {
    max-width: 100%;
    max-height: 85vh;
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 8px;
    display: block;
    margin: 0 auto;
}

@media (max-width: 767px) {
    #modalImage {
        max-width: 96vw;
        max-height: 80vh;
    }
}

/* Loader للصور */
.image-loader {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 200px;
    background: #f8f9fa;
    border-radius: 8px;
    position: relative;
}

.image-loader .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #4a90e2;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.question-image {
    position: relative;
}

.question-image .quiz-image-preview {
    display: none;
}

.question-image .quiz-image-preview.loaded {
    display: block;
}

</style>
<form action="{{ route('quiz.submit', $quiz->id) }}" method="post" class="quiz-submit-form">
    @csrf
    <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">

    @foreach ($questions as $key => $question)
        <div class="question mb-4 @if ($key > 0) d-none @endif" data-question-id="{{ $question->id }}">
            <div class="question-title">
                <span class="serial">{{ ++$key }} </span>
                <div class="question-title-content">{!! $question->title !!}</div>
            </div>
            @if(isset($question->question_image))
                <div class="question-image mt-3">
                    <div class="quiz-image-frame">
                        <div class="image-loader" id="loader-{{ $question->id }}">
                            <div class="spinner"></div>
                        </div>
                        <img src="{{ asset($question->question_image) }}"
                             class="quiz-image-preview preview-img"
                             alt="Question Image"
                             data-question-id="{{ $question->id }}"
                             onclick="openImageModal('{{ asset($question->question_image) }}')"
                             onload="hideImageLoader({{ $question->id }})"
                             onerror="hideImageLoader({{ $question->id }})">
                        <div class="question-image-actions">
                            <button type="button" class="view-image-btn" onclick="openImageModal('{{ asset($question->question_image) }}')" title="تكبير الصورة">
                                <i class="fi fi-rr-zoom-in"></i>
                                <span>تكبير الصورة</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row gap-0">
                @if ($question->type == 'mcq')
                    @php
                        $options = json_decode($question->options, true) ?? [];
                    @endphp
                    @foreach ($options as $index => $option)
                        @php
                            $isImage = preg_match('/\.(jpg|jpeg|png|gif)$/i', $option);
                        @endphp

                        <div class="col-sm-6 my-2">
                            <div class="answer-container" onclick="selectCheckbox(this)" data-option-id="{{ $option }}-{{ $question->id }}">
                                <div class="custom-checkbox"></div>
                                <input type="checkbox" name="{{ $question->id }}[]" value="{{ $option }}" id="{{ $option }}-{{ $question->id }}">

                                <div class="option-content">
                                    @if(!$isImage)
                                        <span class="text-capitalize">{{ $option }}</span>
                                    @else
                                        <img src="{{ asset($option) }}"
                                            alt="Option Image"
                                            class="option-image">
                                    @endif
                                </div>

                                @if($isImage)
                                    <button type="button" class="view-image-btn" onclick="event.stopPropagation(); openImageModal('{{ asset($option) }}')" title="تكبير الصورة">
                                        <i class="fi fi-rr-zoom-in"></i>
                                        <span>تكبير</span>
                                    </button>
                                @endif
                            </div>
                        </div>



                    @endforeach
                @elseif($question->type == 'fill_blanks')
                    <input type="text" class="form-control tagify" name="{{ $question->id }}" data-role="tagsinput">
                @elseif($question->type == 'true_false')
                    <div class="col-sm-6">
                        <div class="answer-container" onclick="selectCheckbox(this)">
                            <div class="custom-checkbox"></div>
                            <input type="radio" name="{{ $question->id }}" value="true" id="question-{{ $question->id }}-true">
                            <div class="option-content">
                                <span>{{ get_phrase('True') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="answer-container" onclick="selectCheckbox(this)">
                            <div class="custom-checkbox"></div>
                            <input type="radio" name="{{ $question->id }}" value="false" id="question-{{ $question->id }}-false">
                            <div class="option-content">
                                <span>{{ get_phrase('False') }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</form>
<div id="imageModal" class="modal" tabindex="-1">
    <div class="modal-content">
        <span onclick="closeImageModal()">&times;</span>
        <img id="modalImage" src="" alt="صورة السؤال">
    </div>
</div>

@if ($questions->count() > 0)
    <div class="row">
        <div class="col-12 d-flex gap-3 justify-content-center">
            <button type="button" class="eBtn gradient border-0" id="prevBtn" onclick="prevQuestion()">
                <i class="fi fi-rr-angle-small-right"></i>
                السابق
            </button>
            <button type="button" class="eBtn gradient border-0" id="nextBtn"
                onclick="nextQuestion()">
                التالى
                <i class="fi fi-rr-angle-small-left"></i>
            </button>
            @if ($submits->count() < $quiz->retake || get_settings('quiz_submission_mode') === 'secure')
                <button type="button" class="eBtn gradient border-0 d-none" id="submitBtn"
                    onclick="submitQuiz()">تسليم<i class="fi fi-rr-badge-check me-2"></i></button>
            @endif
        </div>
    </div>
@endif

@include('course_player.init')
<script>
// Exam Mode configuration
var examMode = {{ (get_settings('quiz_submission_mode') === 'secure') ? 'true' : 'false' }};
var examSubmissionId = window.examSubmissionId || null;
var saveAnswerUrl = "{{ route('quiz.save.answer') }}";
var loadResultUrl = "{{ route('load.quiz.result') }}";
var examCsrfToken = "{{ csrf_token() }}";
var quizId = "{{ $quiz->id }}";

function openImageModal(src){
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').style.display = 'block';
}

function closeImageModal(){
    document.getElementById('imageModal').style.display = 'none';
}

document.getElementById('imageModal').addEventListener('click', function(e){
    if(e.target === this){
        closeImageModal();
    }
});

// Save and grade answer immediately (Exam Mode)
// Returns a Promise so we can wait for save to complete
function saveAndGradeAnswer(questionId) {
    if (!examMode || !examSubmissionId) return Promise.resolve();
    
    var questionDiv = document.querySelector('.question[data-question-id="' + questionId + '"]');
    if (!questionDiv) return Promise.resolve();
    
    var answer = null;
    
    // Check for MCQ (checkboxes)
    var checkboxes = questionDiv.querySelectorAll('input[type="checkbox"][name="' + questionId + '[]"]:checked');
    if (checkboxes.length > 0) {
        answer = Array.from(checkboxes).map(function(cb) { return cb.value; });
    }
    
    // Check for True/False (radio)
    var radio = questionDiv.querySelector('input[type="radio"][name="' + questionId + '"]:checked');
    if (radio) {
        answer = radio.value;
    }
    
    // Check for fill_blanks (text input with tagify)
    var textInput = questionDiv.querySelector('input[type="text"][name="' + questionId + '"]') || questionDiv.querySelector('input.tagify[name="' + questionId + '"]');
    var tagifyValue = null;
    if (textInput) {
        if (textInput.tagify && textInput.tagify.value && textInput.tagify.value.length > 0) {
            tagifyValue = textInput.tagify.value.map(function(item) { return (item && item.value) ? item.value : String(item); });
        } else if (textInput.value && textInput.value.trim() !== '') {
            tagifyValue = textInput.value;
        }
    }
    if (tagifyValue) {
        if (Array.isArray(tagifyValue)) {
            answer = tagifyValue;
        } else {
            try {
                var parsed = JSON.parse(tagifyValue);
                answer = Array.isArray(parsed) ? parsed.map(function(item) { return (item && item.value) ? item.value : (typeof item === 'string' ? item : String(item)); }) : [tagifyValue];
            } catch (e) {
                answer = String(tagifyValue).split(',').map(function(s) { return s.trim(); }).filter(function(s) { return s; });
            }
        }
    }
    
    if (answer === null || (Array.isArray(answer) && answer.length === 0)) return Promise.resolve();
    
    // Send AJAX request to save and grade answer
    return fetch(saveAnswerUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': examCsrfToken
        },
        body: JSON.stringify({
            submission_id: examSubmissionId,
            question_id: questionId,
            answer: answer,
            _token: examCsrfToken
        })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showSaveIndicator(questionDiv, data.is_correct);
        }
        return data;
    })
    .catch(function(error) {
        console.error('Error saving answer:', error);
        return { success: false };
    });
}

// Show indicator that answer was saved
function showSaveIndicator(questionDiv, isCorrect) {
    var indicator = questionDiv.querySelector('.save-indicator');
    if (!indicator) {
        indicator = document.createElement('span');
        indicator.className = 'save-indicator';
        indicator.style.cssText = 'font-size: 12px; margin-right: 10px; transition: opacity 0.3s;';
        questionDiv.querySelector('.question-title').appendChild(indicator);
    }
    indicator.style.color = '#28a745';
    indicator.innerHTML = '<i class="fi fi-rr-check-circle"></i> تم الحفظ';
    indicator.style.opacity = '1';
    setTimeout(function() {
        indicator.style.opacity = '0';
    }, 2000);
}

function selectCheckbox(container){
    var input = container.querySelector('input[type="checkbox"], input[type="radio"]');
    if(input){
        if(input.type === 'radio'){
            // For radio buttons, uncheck all others in the same group
            var name = input.name;
            document.querySelectorAll('input[type="radio"][name="' + name + '"]').forEach(function(radio) {
                var radioContainer = radio.closest('.answer-container');
                if(radioContainer){
                    radioContainer.classList.remove('selected');
                }
                radio.checked = false;
            });
            // Check the clicked one
            input.checked = true;
            container.classList.add('selected');
            
            // Save and grade in Exam Mode
            saveAndGradeAnswer(name);
        } else {
            // For checkboxes, toggle
            input.checked = !input.checked;
            if(input.checked){
                container.classList.add('selected');
            } else {
                container.classList.remove('selected');
            }
            
            // Save and grade in Exam Mode
            var questionId = input.name.replace('[]', '');
            saveAndGradeAnswer(questionId);
        }
    }
}

// Initialize selected state on page load
(function() {
    document.querySelectorAll('.answer-container').forEach(function(container) {
        var checkbox = container.querySelector('input[type="checkbox"], input[type="radio"]');
        if(checkbox && checkbox.checked){
            container.classList.add('selected');
        }
    });
    
    // Add blur listener for fill_blanks inputs (Exam Mode)
    if (examMode) {
        document.querySelectorAll('input.tagify').forEach(function(input) {
            input.addEventListener('blur', function() {
                var questionId = this.name;
                saveAndGradeAnswer(questionId);
            });
        });
    }
})();

function initQuizTitleImages() {
    document.querySelectorAll('.question-title-content img').forEach(function(img) {
        if (img.dataset.quizImageReady === '1') {
            return;
        }

        function setupImage() {
            if (img.dataset.quizImageReady === '1') {
                return;
            }

            img.dataset.quizImageReady = '1';
            img.classList.add('question-rich-image');

            var naturalWidth = img.naturalWidth || 0;
            var naturalHeight = img.naturalHeight || 0;
            if (naturalWidth > 0 && naturalHeight > 0 && naturalWidth / naturalHeight > 1.6) {
                if (!img.closest('.quiz-inline-image-scroll')) {
                    var scrollWrap = document.createElement('div');
                    scrollWrap.className = 'quiz-inline-image-scroll';
                    img.parentNode.insertBefore(scrollWrap, img);
                    scrollWrap.appendChild(img);
                }
            }

            img.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                openImageModal(this.src);
            });
        }

        if (img.complete && img.naturalWidth > 0) {
            setupImage();
        } else {
            img.addEventListener('load', setupImage, { once: true });
        }
    });
}

// إخفاء loader الصورة عند التحميل
function hideImageLoader(questionId) {
    var loader = document.getElementById('loader-' + questionId);
    var img = document.querySelector('img.quiz-image-preview[data-question-id="' + questionId + '"]');

    if (loader) {
        loader.style.display = 'none';
    }

    if (img) {
        img.classList.add('loaded');
    }
}

initQuizTitleImages();

</script>
<script>
    let nextBtn = document.querySelector('#nextBtn');
    let prevBtn = document.querySelector('#prevBtn');
    let submitBtn = document.querySelector('#submitBtn');
    let submitForm = document.querySelector('.quiz-submit-form');
    // next question - save current answer before moving (Exam Mode)
    function nextQuestion() {
        let selectQuestion = document.querySelector('.question:not(.d-none)');
        if (selectQuestion && examMode && examSubmissionId) {
            var currentQuestionId = selectQuestion.getAttribute('data-question-id');
            if (currentQuestionId) {
                saveAndGradeAnswer(currentQuestionId);
            }
        }
        let nextQuestion = selectQuestion ? selectQuestion.nextElementSibling : null;
        if (nextQuestion && nextQuestion.classList.contains('question')) {
            selectQuestion.classList.add('d-none');
            nextQuestion.classList.remove('d-none');
        }
        let nextNextQuestion = nextQuestion ? nextQuestion.nextElementSibling : null;
        if (!nextQuestion || !(nextNextQuestion && nextNextQuestion.classList.contains('question'))) {
            if (submitBtn) {
                submitBtn.classList.remove('d-none');
                nextBtn.classList.add('d-none');
            }
        }
    }

    // previous question - save current answer before moving (Exam Mode)
    function prevQuestion() {
        let selectQuestion = document.querySelector('.question:not(.d-none)');
        if (selectQuestion && examMode && examSubmissionId) {
            var currentQuestionId = selectQuestion.getAttribute('data-question-id');
            if (currentQuestionId) {
                saveAndGradeAnswer(currentQuestionId);
            }
        }
        let prevQuestion = selectQuestion.previousElementSibling;
        if (prevQuestion && prevQuestion.classList.contains('question')) {
            selectQuestion.classList.add('d-none');
            prevQuestion.classList.remove('d-none');
        }
        if (nextBtn.classList.contains('d-none')) {
            nextBtn.classList.remove('d-none');
            if (submitBtn) submitBtn.classList.add('d-none');
        }
    }

    // submit quiz
    function submitQuiz() {
        if (examMode && examSubmissionId) {
            // Exam mode: show confirmation then save all answers before redirect
            Swal.fire({
                title: 'تأكيد التسليم',
                text: 'هل أنت متأكد أنك تريد تسليم الاختبار؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، سلم الاختبار',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                reverseButtons: true
            }).then((result) => {
                if (!result.isConfirmed) return;
                var savePromises = [];
                document.querySelectorAll('.question').forEach(function(q) {
                    var qId = q.getAttribute('data-question-id');
                    if (qId) savePromises.push(saveAndGradeAnswer(qId));
                });
                Promise.all(savePromises).then(function() {
                    var url = new URL(window.location.href);
                    url.searchParams.set('show_result', examSubmissionId);
                    window.location.href = url.toString();
                }).catch(function() {
                    var url = new URL(window.location.href);
                    url.searchParams.set('show_result', examSubmissionId);
                    window.location.href = url.toString();
                });
            });
            return;
        }
        Swal.fire({
            title: 'تأكيد التسليم',
            text: 'هل أنت متأكد أنك تريد تسليم الاختبار؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، سلم الاختبار',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm.submit();
            }
        });
    }
</script>
