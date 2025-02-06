document.addEventListener('DOMContentLoaded', function () {
    const avaToggle = document.querySelector('.avatar-toggle');
    const navMypage = document.querySelector('.nav-myPage');
    const overlayAva = document.querySelector('.overlay-avatar');
    const avatar = document.querySelector('.avatar');

   
    avaToggle.addEventListener('click', function () {
        navMypage.classList.toggle('open');
        avatar.classList.toggle('open'); 
    });


    overlayAva.addEventListener('click', function () {
        navMypage.classList.remove('open');
        avatar.classList.remove('open'); 
    });
});