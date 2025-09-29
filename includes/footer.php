</main>

<script>
function toggleTheme() {
    const body = document.body;
    const current = localStorage.getItem('theme');
    const next = current === 'theme-light' ? 'theme-dark' : 'theme-light';
    body.classList.remove('theme-light', 'theme-dark');
    body.classList.add(next);
    localStorage.setItem('theme', next);
}

document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('theme') || 'theme-dark';
    document.body.classList.add(saved);
});
</script>

<div class="container py-4 text-center">
    <p>📞 Contacto: <a href="mailto:info@perfumesverdes.pt">info@perfumesverdes.pt</a> | ☎️ 912 345 678</p>
    <p>
        <a href="https://facebook.com" target="_blank" class=" me-2"><i class="bi bi-facebook"></i></a>
        <a href="https://instagram.com" target="_blank" class=" me-2"><i class="bi bi-instagram"></i></a>
        <a href="https://tiktok.com" target="_blank" class=""><i class="bi bi-tiktok"></i></a>
    </p>
</div>


<footer>
    <div class="footer-content">
        <p>&copy; <?php echo date("Y"); ?> Perfumes Verdes. Todos os direitos reservados.</p>
    </div>
</footer>

<!-- Bootstrap JS (com Popper incluído) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
