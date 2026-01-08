    </div> <!-- .container kapanışı -->

    <footer class="bg-light py-3 mt-5">
        <div class="container text-center">
            <small>&copy; <?= date('Y') ?> Online Kurs Platformu</small>
        </div>
    </footer>

    <!-- jQuery + Bootstrap JS (TEK KEZ YÜKLÜ OLMALI) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="/online_course_platform/assets/js/main.js"></script>

    <!-- 🔹 DERS ÖNİZLEME MODALI (SENDE ZATEN VAR – BİZ DOKUNMADIK) -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var lessonPreviewModal = document.getElementById('lessonPreviewModal');
        if (lessonPreviewModal) {
            lessonPreviewModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                if (!button) return;

                var title   = button.getAttribute('data-title')   || '';
                var content = button.getAttribute('data-content') || '';
                var video   = button.getAttribute('data-video')   || '';

                var modalTitle   = lessonPreviewModal.querySelector('#lessonPreviewTitle');
                var modalContent = lessonPreviewModal.querySelector('#lessonPreviewContent');
                var modalVideo   = lessonPreviewModal.querySelector('#lessonPreviewVideo');

                if (modalTitle)   modalTitle.textContent = title;
                if (modalContent) modalContent.textContent = content;

                if (modalVideo) {
                    if (video) {
                        modalVideo.innerHTML =
                            '<video width="100%" controls>' +
                                '<source src="' + video + '" type="video/mp4">' +
                                'Tarayıcınız video etiketini desteklemiyor.' +
                            '</video>';
                    } else {
                        modalVideo.innerHTML = '';
                    }
                }
            });
        }
    });
    </script>

    <!-- 🔹 DERS TAMAMLAMA / İLERLEME JS (SENDE VAR OLAN) -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('form.lesson-progress-form');

        forms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                if (!submitBtn) return;

                const originalText = submitBtn.textContent;
                submitBtn.disabled = true;
                submitBtn.textContent = 'Kaydediliyor...';

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.status !== 'ok') {
                        alert('Bir hata oluştu: ' + (data.message || 'Bilinmeyen hata'));
                        return;
                    }

                    if (data.completed) {
                        submitBtn.textContent = 'Tamamlandı';
                        submitBtn.classList.remove('btn-outline-secondary');
                        submitBtn.classList.add('btn-success');
                    } else {
                        submitBtn.textContent = 'Tamamla';
                        submitBtn.classList.remove('btn-success');
                        submitBtn.classList.add('btn-outline-secondary');
                    }

                    const bar = document.querySelector('.progress .progress-bar');
                    if (bar && typeof data.progressPercent !== 'undefined') {
                        const p = data.progressPercent;
                        bar.style.width = p + '%';
                        bar.setAttribute('aria-valuenow', p);
                        bar.textContent = '%' + p;
                    }
                })
                .catch(function (err) {
                    console.error(err);
                    alert('Sunucuya bağlanırken bir hata oluştu.');
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    if (submitBtn.textContent === 'Kaydediliyor...') {
                        submitBtn.textContent = originalText;
                    }
                });
            });
        });
    });
    </script>

    <!-- 🔹 🔥 KURS ÖNİZLEME MODALI (BİZİM EKLEDİĞİMİZ YENİ KISIM) -->
    <script>
    $(document).ready(function () {

        // Önizle butonuna tıklanınca
        $('.btn-preview-course').on('click', function () {
            const btn = $(this);

            $('#previewTitle').text(btn.data('title'));
            $('#previewDescription').text(btn.data('description'));
            $('#previewImage').attr('src', btn.data('image'));
            $('#previewInstructor').text(btn.data('instructor'));
            $('#previewCategory').text(btn.data('category'));
            $('#previewLevel').text(btn.data('level'));
            $('#previewPrice').text(btn.data('price'));
            $('#previewRating').text(btn.data('rating'));

            const modal = new bootstrap.Modal(document.getElementById('coursePreviewModal'));
            modal.show();
        });

        // Arama + kategori filtresi
        function filterCourses() {
            const search = $('#courseSearch').val().toLowerCase();
            const category = $('#categoryFilter').val();

            $('.course-card').each(function () {
                const card = $(this);
                const title = card.data('title');
                const catId = card.data('category').toString();

                const matchTitle = !search || title.includes(search);
                const matchCategory = !category || category === catId;

                card.toggle(matchTitle && matchCategory);
            });
        }

        $('#courseSearch').on('keyup', filterCourses);
        $('#categoryFilter').on('change', filterCourses);
    });
    </script>

    <!-- Smooth Scroll Script -->
<script>
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener("click", function(e) {
        const target = document.querySelector(this.getAttribute("href"));
        if (!target) return;

        e.preventDefault();
        target.scrollIntoView({ behavior: "smooth" });
    });
});
</script>


</body>
</html>
