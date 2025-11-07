document.querySelectorAll('#rating i').forEach(star => {
    star.addEventListener('click', function () {
        let value = this.dataset.value;
        let course = document.querySelector('#rating').dataset.course;

        fetch("rate.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: `course_id=${course}&rating=${value}`
        })
        .then(res => res.json())
        .then(() => location.reload());
    });
});
