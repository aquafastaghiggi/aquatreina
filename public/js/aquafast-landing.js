document.querySelectorAll('a[href^="#"]').forEach(link=>{
  link.addEventListener('click',e=>{
    const target=document.querySelector(link.getAttribute('href'));
    if(!target)return;
    e.preventDefault();
    target.scrollIntoView({behavior:'smooth',block:'start'});
  });
});

const menuToggle=document.querySelector('.menu-toggle');
const navLinks=document.getElementById('nav-links');
if(menuToggle&&navLinks){
  menuToggle.addEventListener('click',()=>{
    const aberto=navLinks.classList.toggle('is-open');
    menuToggle.setAttribute('aria-expanded',aberto?'true':'false');
  });
  navLinks.querySelectorAll('a').forEach(link=>{
    link.addEventListener('click',()=>{
      navLinks.classList.remove('is-open');
      menuToggle.setAttribute('aria-expanded','false');
    });
  });
}
