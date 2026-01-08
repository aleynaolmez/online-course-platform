document.addEventListener("DOMContentLoaded", function () {

    /* ----------------------------------------------------------
     * 1) DERS ÖNİZLEME MODALINI DOLDUR
     * ---------------------------------------------------------- */
    const previewButtons = document.querySelectorAll(".lesson-preview-btn");
    const modalTitle = document.getElementById("lessonPreviewTitle");
    const modalContent = document.getElementById("lessonPreviewContent");
    const modalVideo = document.getElementById("lessonPreviewVideo");

    previewButtons.forEach(btn => {
        btn.addEventListener("click", function () {
            const title = this.dataset.title || "";
            const content = this.dataset.content || "";
            const video = this.dataset.video || "";

            modalTitle.textContent = title;
            modalContent.textContent = content;

            if (video) {
                modalVideo.innerHTML =
                    `<div class="ratio ratio-16x9">
                        <iframe src="${video}" frameborder="0" allowfullscreen></iframe>
                     </div>`;
            } else {
                modalVideo.innerHTML = "";
            }
        });
    });

    /* ----------------------------------------------------------
     * 2) DERS TAMAMLAMA (AJAX)
     * ---------------------------------------------------------- */
    const progressForms = document.querySelectorAll(".lesson-progress-form");

    progressForms.forEach(form => {
        form.addEventListener("submit", function (e) {
            e.preventDefault(); // sayfa yenilenmesin

            const formData = new FormData(form);

            fetch("../actions/toggle_lesson_progress.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    console.log("Progress Response:", data);

                    if (data.status === "ok") {

                        // Butonun kendisini değiştir
                        const btn = form.querySelector("button");
                        if (data.completed) {
                            btn.classList.remove("btn-outline-secondary");
                            btn.classList.add("btn-success");
                            btn.textContent = "Tamamlandı";
                        } else {
                            btn.classList.remove("btn-success");
                            btn.classList.add("btn-outline-secondary");
                            btn.textContent = "Tamamla";
                        }

                        // İlerleme barını güncelle
                        updateProgressUI(data.totalLessons, data.completedCount, data.progressPercent);
                    }
                })
                .catch(err => {
                    console.error("Fetch error:", err);
                });
        });
    });


    /* ----------------------------------------------------------
     * 3) İLERLEME ÇUBUĞU GÜNCELLEME FONKSİYONU
     * ---------------------------------------------------------- */
    function updateProgressUI(total, completed, percent) {

        const bar = document.getElementById("courseProgressBar");
        const label = document.getElementById("courseProgressLabel");

        if (!bar || !label) return;

        bar.style.width = percent + "%";
        bar.setAttribute("aria-valuenow", percent);
        bar.textContent = "%" + percent;

        label.textContent = `İlerleme: ${completed}/${total} ders (%${percent})`;
    }

});
