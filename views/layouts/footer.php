<!-- views/layouts/footer.php -->
</main>

<footer class="bg-white premium-shadow">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <!-- About Section -->
            <div class="space-y-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-blue-400 rounded-lg flex items-center justify-center">
                        <span class="text-white text-xl font-bold">P</span>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-400 text-transparent bg-clip-text">PAI Digital</span>
                </div>
                <p class="text-gray-600 leading-relaxed">
                    Platform pembelajaran digital yang dirancang khusus untuk meningkatkan pemahaman siswa dalam Pendidikan Agama Islam melalui teknologi modern.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-blue-100 flex items-center justify-center text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-blue-100 flex items-center justify-center text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-blue-100 flex items-center justify-center text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-900">Tautan Cepat</h3>
                <ul class="space-y-4">
                    <li>
                        <a href="/tentang" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                            <i class="fas fa-chevron-right text-xs mr-2"></i>
                            Tentang Kami
                        </a>
                    </li>
                    <li>
                        <a href="/fitur" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                            <i class="fas fa-chevron-right text-xs mr-2"></i>
                            Fitur
                        </a>
                    </li>
                    <li>
                        <a href="/blog" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                            <i class="fas fa-chevron-right text-xs mr-2"></i>
                            Blog
                        </a>
                    </li>
                    <li>
                        <a href="/faq" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center">
                            <i class="fas fa-chevron-right text-xs mr-2"></i>
                            FAQ
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-900">Hubungi Kami</h3>
                <ul class="space-y-4">
                    <li class="flex items-start space-x-3 text-gray-600">
                        <i class="fas fa-map-marker-alt mt-1"></i>
                        <span>Jl. Pendidikan No. 123<br />Jakarta Selatan, 12345</span>
                    </li>
                    <li class="flex items-center space-x-3 text-gray-600">
                        <i class="fas fa-phone"></i>
                        <span>(021) 1234-5678</span>
                    </li>
                    <li class="flex items-center space-x-3 text-gray-600">
                        <i class="fas fa-envelope"></i>
                        <span>info@paidigital.com</span>
                    </li>
                    <li class="flex items-center space-x-3 text-gray-600">
                        <i class="fas fa-clock"></i>
                        <span>Senin - Jumat: 08:00 - 16:00</span>
                    </li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="space-y-6">
                <h3 class="text-lg font-semibold text-gray-900">Berita & Update</h3>
                <p class="text-gray-600">Berlangganan newsletter kami untuk mendapatkan update terbaru.</p>
                <form class="space-y-3">
                    <div class="flex">
                        <input type="email" placeholder="Email Anda" class="flex-1 px-4 py-2 rounded-l-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-r-lg hover:from-blue-700 hover:to-blue-800 transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="text-gray-600 text-sm">
                    &copy; <?php echo date('Y'); ?> PAI Digital. All rights reserved.
                    <a href="/privacy" class="text-blue-600 hover:underline ml-4">Privacy Policy</a>
                    <a href="/terms" class="text-blue-600 hover:underline ml-4">Terms of Service</a>
                </div>
                <div class="flex justify-start md:justify-end space-x-4">
                    <img src="/assets/images/payment/visa.png" alt="Visa" class="h-8">
                    <img src="/assets/images/payment/mastercard.png" alt="Mastercard" class="h-8">
                    <img src="/assets/images/payment/paypal.png" alt="PayPal" class="h-8">
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button id="backToTop" class="fixed bottom-8 right-8 w-12 h-12 rounded-full bg-blue-600 text-white shadow-lg flex items-center justify-center transform transition-all duration-300 translate-y-20 opacity-0">
        <i class="fas fa-arrow-up"></i>
    </button>
</footer>

<script>
    // Back to Top Button
    const backToTop = document.getElementById('backToTop');

    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTop.classList.remove('translate-y-20', 'opacity-0');
        } else {
            backToTop.classList.add('translate-y-20', 'opacity-0');
        }
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Newsletter Form Animation
    const newsletterForm = document.querySelector('form');
    newsletterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const button = newsletterForm.querySelector('button');
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.add('bg-green-500');
        setTimeout(() => {
            button.innerHTML = '<i class="fas fa-paper-plane"></i>';
            button.classList.remove('bg-green-500');
        }, 2000);
    });
</script>
</body>

</html>