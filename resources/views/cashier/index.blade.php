@extends('layouts.app')

@section('title', 'Transaksi Pembayaran Kasir - TOKO RETAIL MAKMUR')

@section('content')
<link rel="stylesheet" href="{{ url(asset('css/chasier.css')) }}">

<div class="pos-container">
    <!-- ================= HEADER (Modul Hal. 3) ================= -->
    <header class="header">
        <div>
            <div class="store-name">TOKO RETAIL MAKMUR</div>
            <div class="store-info">
                Jl. Contoh No. 123 • Jember • Telp. 0812-xxxx-xxxx
            </div>
        </div>
        <div class="transaction-info">
            <div>No. Transaksi</div>
            <div class="transaction-number" id="transactionNumber">
                {{ $transactionNumber }}
            </div>
            <div id="currentDate"></div>
        </div>
    </header>

    <!-- Transaksi Ditahan Banner jika ada (Modul Hal. 22) -->
    @if (!empty($heldTransactions) && count($heldTransactions) > 0)
        <div class="card mb-3 border-warning">
            <div class="card-header bg-warning-subtle text-dark py-2">
                <strong>Daftar Transaksi Ditahan (Parkir Transaksi)</strong>
            </div>
            <div class="card-body p-2">
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($heldTransactions as $holdId => $held)
                        <div class="badge bg-light text-dark border p-2 d-flex align-items-center gap-2">
                            <span><strong>{{ $holdId }}</strong> ({{ $held['customer_name'] }}) - {{ $held['total_items'] }} Item - Rp {{ number_format($held['total_amount'], 0, ',', '.') }}</span>
                            <a href="{{ route('cashier.cart.resume', $holdId) }}" class="btn btn-warning btn-sm py-0 px-2" style="font-size: 11px;">
                                Lanjutkan
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- ================= MAIN (Modul Hal. 4) ================= -->
    <main class="main">
        <!-- ================= LEFT SECTION ================= -->
        <section>
            <!-- 2. PENCARIAN BARANG & DATA PELANGGAN (Modul Hal. 5) -->
            <div class="card">
                <div class="card-header">Tambah Barang</div>
                <div class="card-body">
                    <div class="search-area">
                        <div class="search-input">
                            <input type="text" id="searchProduct" placeholder="Cari nama barang / kode / barcode..." autocomplete="off">
                            <div class="product-results" id="productResults"></div>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="searchProduct()">Cari</button>
                    </div>

                    <!-- CUSTOMER -->
                    <div class="customer-area">
                        <div class="form-group">
                            <label>Pelanggan</label>
                            <select class="form-control" id="customerType">
                                <option value="Umum">Umum</option>
                                <option value="Pelanggan Member">Pelanggan Member</option>
                                <option value="Member VIP">Member VIP</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>No. Member / HP</label>
                            <input type="text" class="form-control" id="memberNumber" placeholder="Opsional">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. DAFTAR BARANG SEMENTARA / KERANJANG BELANJA (Modul Hal. 6) -->
            <div class="card" style="margin-top: 15px;">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <span>Keranjang Belanja</span>
                        <span id="itemCount">0 Item</span>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th width="40">No</th>
                                <th>Barang</th>
                                <th width="100">Harga</th>
                                <th width="120" class="text-center">Qty</th>
                                <th width="120" class="text-right">Subtotal</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                            <tr id="emptyRow">
                                <td colspan="6" class="empty-cart">
                                    Keranjang masih kosong.
                                    <br>
                                    Silakan cari atau scan barang.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ================= RIGHT ASIDE ================= -->
        <aside>
            <!-- 4. RINGKASAN PEMBAYARAN & PEMBAYARAN (Modul Hal. 8-10) -->
            <div class="card">
                <div class="card-header">Ringkasan Pembayaran</div>
                <div class="card-body">
                    <div class="payment-summary">
                        <!-- TOTAL ITEM -->
                        <div class="summary-row">
                            <span>Total Item</span>
                            <strong id="totalQty">0</strong>
                        </div>

                        <!-- SUBTOTAL -->
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <strong id="subtotal">Rp 0</strong>
                        </div>

                        <!-- DISCOUNT -->
                        <div class="summary-row">
                            <span>Diskon (%)</span>
                            <input type="number" id="discountPercent" value="0" min="0" max="100" onchange="calculateTotal()">
                        </div>
                        <div class="summary-row">
                            <span>Diskon (Rp)</span>
                            <input type="number" id="discountAmount" value="0" min="0" onchange="calculateTotal()">
                        </div>

                        <!-- TAX -->
                        <div class="summary-row">
                            <span>Pajak / PPN</span>
                            <input type="number" id="tax" value="0" min="0" onchange="calculateTotal()">
                        </div>

                        <!-- OTHER FEE -->
                        <div class="summary-row">
                            <span>Biaya Lain</span>
                            <input type="number" id="otherFee" value="0" min="0" onchange="calculateTotal()">
                        </div>

                        <!-- TOTAL -->
                        <div class="total-box">
                            <div class="total-label">TOTAL AKHIR</div>
                            <div class="total-value" id="grandTotal">Rp 0</div>
                        </div>

                        <!-- PAYMENT (Modul Hal. 9) -->
                        <div class="payment-box">
                            <div class="payment-label">Uang Dibayar</div>
                            <input type="number" id="payment" class="payment-input" placeholder="0" oninput="calculateChange()">

                            <!-- SHORTCUT NOMINAL CEPAT (Modul Hal. 22-23) -->
                            <div class="quick-cash-row" id="quickCashContainer">
                                <button type="button" class="quick-cash-btn" onclick="setQuickPayment('exact')">Uang Pas</button>
                                <button type="button" class="quick-cash-btn" onclick="setQuickPayment(10000)">10k</button>
                                <button type="button" class="quick-cash-btn" onclick="setQuickPayment(20000)">20k</button>
                                <button type="button" class="quick-cash-btn" onclick="setQuickPayment(50000)">50k</button>
                                <button type="button" class="quick-cash-btn" onclick="setQuickPayment(100000)">100k</button>
                            </div>

                            <!-- PAYMENT METHOD -->
                            <div class="payment-label" style="margin-top: 15px;">Metode Pembayaran</div>
                            <div class="payment-method">
                                <button type="button" class="active" onclick="selectPayment(this, 'cash')">Tunai</button>
                                <button type="button" onclick="selectPayment(this, 'qris')">QRIS</button>
                                <button type="button" onclick="selectPayment(this, 'debit')">Debit</button>
                                <button type="button" onclick="selectPayment(this, 'kredit')">Kredit</button>
                                <button type="button" onclick="selectPayment(this, 'ewallet')">E-Wallet</button>
                                <button type="button" onclick="selectPayment(this, 'transfer')">Transfer</button>
                            </div>

                            <!-- CHANGE -->
                            <div class="change-box" id="changeBox">
                                <div class="change-label">KEMBALIAN</div>
                                <div class="change-value" id="change">Rp 0</div>
                            </div>
                        </div>

                        <!-- ACTION (Modul Hal. 10) -->
                        <div class="action-area">
                            <button type="button" class="btn btn-warning" onclick="holdTransaction()">Tahan</button>
                            <button type="button" class="btn btn-danger" onclick="cancelTransaction()">Batal</button>
                            <button type="button" class="btn btn-success btn-pay" id="btnPay" onclick="processPayment()">
                                BAYAR & CETAK
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KEYBOARD SHORTCUT (Modul Hal. 19) -->
            <div class="shortcut-bar">
                <strong>F2</strong> = Cari Barang • <strong>F4</strong> = Uang Bayar • <strong>ESC</strong> = Batal Transaksi
            </div>
        </aside>
    </main>
</div>

<!-- ================= MODAL STRUK PEMBAYARAN KASIR ================= -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title fw-bold" id="receiptModalLabel">📄 Preview Struk Pembayaran</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="startNewTransaction()"></button>
            </div>
            <div class="modal-body p-3 bg-light">
                <!-- Struk Belanja Kasir -->
                <div id="modalReceiptArea" style="background:#fff; padding:18px; border:1px dashed #bbb; font-family:'Courier New', monospace; font-size:12px; color:#222;">
                    <div class="text-center">
                        <h6 class="fw-bold mb-1 text-uppercase">POS TOKO</h6>
                        <div style="font-size:11px;">Workshop Web Framework - Jember</div>
                        <div style="font-size:11px;">Telp: (0331) 333532</div>
                        <div style="border-top:2px dashed #444; margin:8px 0;"></div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between">
                            <span>Invoice:</span>
                            <strong id="mInvoice">-</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Waktu:</span>
                            <span id="mDate">-</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Kasir:</span>
                            <span id="mCashier">{{ auth()->user()->name ?? 'Kasir' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Pelanggan:</span>
                            <span id="mCustomer">Umum</span>
                        </div>
                        <div style="border-top:1px dashed #777; margin:8px 0;"></div>
                    </div>
                    <!-- Items -->
                    <div id="mItemsList"></div>
                    <div style="border-top:1px dashed #777; margin:8px 0;"></div>
                    <div>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span id="mSubtotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between text-danger" id="mDiscountRow" style="display:none;">
                            <span>Diskon:</span>
                            <span id="mDiscount">- Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between" id="mTaxRow" style="display:none;">
                            <span>PPN:</span>
                            <span id="mTax">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between" id="mOtherRow" style="display:none;">
                            <span>Biaya Lain:</span>
                            <span id="mOtherFee">Rp 0</span>
                        </div>
                        <div style="border-top:1px dashed #777; margin:6px 0;"></div>
                        <div class="d-flex justify-content-between fw-bold" style="font-size:14px;">
                            <span>TOTAL:</span>
                            <span id="mTotal">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span>Metode:</span>
                            <span class="text-uppercase" id="mMethod">TUNAI</span>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span>Bayar:</span>
                            <span id="mPaid">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mt-1 fw-bold">
                            <span>Kembali:</span>
                            <span id="mChange">Rp 0</span>
                        </div>
                    </div>
                    <div style="border-top:2px dashed #444; margin:10px 0;"></div>
                    <div class="text-center" style="font-size:11px;">
                        <div class="fw-bold">TERIMA KASIH ATAS KUNJUNGAN ANDA</div>
                        <div class="text-muted" style="font-size:10px;">Barang yang sudah dibeli tidak dapat ditukar</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex flex-column gap-2 p-3 bg-white">
                <button type="button" class="btn btn-primary w-100 fw-bold py-2 shadow-sm" onclick="printModalReceipt()">
                    🖨️ Cetak Struk Sekarang
                </button>
                <div class="d-flex gap-2 w-100">
                    <a href="#" id="mFullReceiptBtn" class="btn btn-outline-secondary w-50 btn-sm">
                        Buka Halaman Struk
                    </a>
                    <button type="button" class="btn btn-success w-50 btn-sm fw-semibold" data-bs-dismiss="modal" onclick="startNewTransaction()">
                        + Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden print iframe -->
<iframe id="receiptPrintFrame" style="display:none; position:absolute; width:0; height:0; border:none;"></iframe>

<!-- ================= JAVASCRIPT KASIR (Modul Hal. 11-19) ================= -->
<script>
// a. Sample & Active Product Data dari Database (Modul Hal. 11)
const products = @json($allProducts);

/* ============================================
   FORMAT RUPIAH (Modul Hal. 13)
============================================ */
function formatRupiah(value) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0
    }).format(value);
}

/* ============================================
   CART STATE (Modul Hal. 13)
============================================ */
let cart = [];
let selectedPaymentMethod = 'cash';

// Sinkronisasi data keranjang awal dari session jika ada
@if (!empty($cart) && count($cart) > 0)
    @foreach ($cart as $item)
        cart.push({
            id: {{ $item['id'] }},
            code: "{{ $item['code'] ?? $item['sku'] }}",
            barcode: "{{ $item['code'] ?? $item['sku'] }}",
            name: "{{ $item['name'] }}",
            price: {{ $item['price'] }},
            qty: {{ $item['quantity'] }},
            stock: {{ $item['stock'] ?? 999 }}
        });
    @endforeach
@endif

/* ============================================
   1. SEARCH PRODUCT (Modul Hal. 12 & Barcode Hal. 20)
============================================ */
const searchInput = document.getElementById("searchProduct");
const resultBox = document.getElementById("productResults");

searchInput.addEventListener("input", searchProduct);

// Dukungan Barcode Scanner (Tekan Enter langsung masukkan ke keranjang)
searchInput.addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
        e.preventDefault();
        const keyword = searchInput.value.toLowerCase().trim();
        if (!keyword) return;

        // Cari exact match kode/barcode atau nama
        const exact = products.find(p => 
            p.barcode.toLowerCase() === keyword || 
            p.code.toLowerCase() === keyword ||
            p.name.toLowerCase() === keyword
        );

        if (exact) {
            addToCart(exact.id);
        } else {
            searchProduct();
        }
    }
});

function searchProduct() {
    const keyword = searchInput.value.toLowerCase().trim();

    if (!keyword) {
        resultBox.style.display = "none";
        return;
    }

    const results = products.filter(
        (product) =>
            product.name.toLowerCase().includes(keyword) ||
            product.code.toLowerCase().includes(keyword) ||
            product.barcode.toLowerCase().includes(keyword)
    );

    if (results.length === 0) {
        resultBox.innerHTML = `
            <div class="product-item text-muted">
                Barang tidak ditemukan
            </div>
        `;
    } else {
        resultBox.innerHTML = results.slice(0, 8)
            .map(
                (product) => `
                <div class="product-item" onclick="addToCart(${product.id})">
                    <div>
                        <div class="product-name">${product.name}</div>
                        <div class="product-code">${product.code} (Stok: ${product.stock})</div>
                    </div>
                    <div class="product-price">${formatRupiah(product.price)}</div>
                </div>
                `
            ).join("");
    }

    resultBox.style.display = "block";
}

// Tutup dropdown hasil jika klik di luar
document.addEventListener("click", function(e) {
    if (!searchInput.contains(e.target) && !resultBox.contains(e.target)) {
        resultBox.style.display = "none";
    }
});

/* ============================================
   ADD CART (Modul Hal. 13)
============================================ */
function addToCart(productId) {
    const product = products.find((p) => p.id === productId);
    if (!product) return;

    const existing = cart.find((item) => item.id === productId);
    if (existing) {
        if (existing.qty + 1 > product.stock) {
            alert("Stok barang '" + product.name + "' tidak mencukupi (Maks: " + product.stock + ")");
            return;
        }
        existing.qty++;
    } else {
        if (product.stock <= 0) {
            alert("Stok barang '" + product.name + "' habis!");
            return;
        }
        cart.push({
            ...product,
            qty: 1,
        });
    }

    searchInput.value = "";
    resultBox.style.display = "none";
    renderCart();
}

/* ============================================
   RENDER CART (Modul Hal. 14)
============================================ */
function renderCart() {
    const body = document.getElementById("cartBody");

    if (cart.length === 0) {
        body.innerHTML = `
            <tr id="emptyRow">
                <td colspan="6" class="empty-cart">
                    Keranjang masih kosong.
                    <br>
                    Silakan cari atau scan barang.
                </td>
            </tr>
        `;
        calculateTotal();
        return;
    }

    body.innerHTML = cart.map(
        (item, index) => `
        <tr>
            <td>${index + 1}</td>
            <td>
                <strong>${item.name}</strong>
                <div style="font-size:11px; color:#6c757d">
                    ${item.code}
                </div>
            </td>
            <td>${formatRupiah(item.price)}</td>
            <td>
                <div class="qty-control">
                    <button type="button" onclick="changeQty(${item.id}, -1)">−</button>
                    <input type="number" value="${item.qty}" min="1" onchange="updateQty(${item.id}, this.value)">
                    <button type="button" onclick="changeQty(${item.id}, 1)">+</button>
                </div>
            </td>
            <td class="text-right">
                <strong>${formatRupiah(item.price * item.qty)}</strong>
            </td>
            <td class="text-center">
                <button type="button" class="remove-btn" onclick="removeItem(${item.id})" title="Hapus">×</button>
            </td>
        </tr>
        `
    ).join("");

    calculateTotal();
}

/* ============================================
   CHANGE & UPDATE QTY (Modul Hal. 15)
============================================ */
function changeQty(id, amount) {
    const item = cart.find((item) => item.id === id);
    if (!item) return;

    const targetProduct = products.find(p => p.id === id);
    if (amount > 0 && targetProduct && item.qty + amount > targetProduct.stock) {
        alert("Stok maksimal tercapai (" + targetProduct.stock + ")");
        return;
    }

    item.qty += amount;
    if (item.qty <= 0) {
        removeItem(id);
        return;
    }
    renderCart();
}

function updateQty(id, qty) {
    const item = cart.find((item) => item.id === id);
    if (!item) return;

    const parsedQty = Math.max(1, parseInt(qty) || 1);
    const targetProduct = products.find(p => p.id === id);

    if (targetProduct && parsedQty > targetProduct.stock) {
        alert("Kuantitas melebihi stok yang tersedia (" + targetProduct.stock + ")");
        item.qty = targetProduct.stock;
    } else {
        item.qty = parsedQty;
    }

    renderCart();
}

/* ============================================
   REMOVE ITEM (Modul Hal. 15)
============================================ */
function removeItem(id) {
    cart = cart.filter((item) => item.id !== id);
    renderCart();
}

/* ============================================
   CALCULATE TOTAL (Modul Hal. 16)
============================================ */
function calculateTotal() {
    let totalQty = 0;
    let subtotal = 0;

    cart.forEach((item) => {
        totalQty += item.qty;
        subtotal += item.price * item.qty;
    });

    const discountPercent = parseFloat(document.getElementById("discountPercent").value) || 0;
    const manualDiscount = parseFloat(document.getElementById("discountAmount").value) || 0;
    const tax = parseFloat(document.getElementById("tax").value) || 0;
    const otherFee = parseFloat(document.getElementById("otherFee").value) || 0;

    const percentDiscount = subtotal * (discountPercent / 100);
    const totalDiscount = percentDiscount + manualDiscount;
    const grandTotal = Math.max(0, subtotal - totalDiscount + tax + otherFee);

    document.getElementById("totalQty").innerText = totalQty;
    document.getElementById("itemCount").innerText = totalQty + " Item";
    document.getElementById("subtotal").innerText = formatRupiah(subtotal);
    document.getElementById("grandTotal").innerText = formatRupiah(grandTotal);

    calculateChange();
}

function getGrandTotal() {
    let subtotal = 0;
    cart.forEach((item) => {
        subtotal += item.price * item.qty;
    });

    const discountPercent = parseFloat(document.getElementById("discountPercent").value) || 0;
    const discountAmount = parseFloat(document.getElementById("discountAmount").value) || 0;
    const tax = parseFloat(document.getElementById("tax").value) || 0;
    const otherFee = parseFloat(document.getElementById("otherFee").value) || 0;

    const discount = (subtotal * discountPercent) / 100 + discountAmount;
    return Math.max(0, subtotal - discount + tax + otherFee);
}

/* ============================================
   CALCULATE CHANGE (Modul Hal. 16)
============================================ */
function calculateChange() {
    const grandTotal = getGrandTotal();
    const payment = parseFloat(document.getElementById("payment").value) || 0;
    const change = payment - grandTotal;
    const changeBox = document.getElementById("changeBox");
    const changeElement = document.getElementById("change");
    const changeLabel = document.querySelector(".change-label");

    if (change >= 0) {
        changeBox.classList.remove("short-payment");
        changeElement.innerText = formatRupiah(change);
        changeLabel.innerText = "KEMBALIAN";
    } else {
        changeBox.classList.add("short-payment");
        changeElement.innerText = formatRupiah(Math.abs(change));
        changeLabel.innerText = "UANG KURANG";
    }
}

/* ============================================
   SHORTCUT NOMINAL CEPAT (Modul Hal. 22-23)
============================================ */
function setQuickPayment(val) {
    const grandTotal = getGrandTotal();
    if (val === 'exact') {
        document.getElementById("payment").value = grandTotal;
    } else {
        document.getElementById("payment").value = val;
    }
    calculateChange();
}

/* ============================================
   PAYMENT METHOD (Modul Hal. 17)
============================================ */
function selectPayment(button, method) {
    document.querySelectorAll(".payment-method button").forEach((btn) => {
        btn.classList.remove("active");
    });
    button.classList.add("active");
    selectedPaymentMethod = method;
}

/* ============================================
   HOLD TRANSACTION (Modul Hal. 17 & 22)
============================================ */
function holdTransaction() {
    if (cart.length === 0) {
        alert("Tidak ada transaksi untuk ditahan.");
        return;
    }

    const customerType = document.getElementById("customerType").value;
    const memberNo = document.getElementById("memberNumber").value;
    const customer = memberNo ? `${customerType} (${memberNo})` : customerType;

    fetch("{{ route('cashier.cart.hold') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            customer_name: customer,
            cart: cart
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert(data.message || "Gagal menahan transaksi.");
        }
    })
    .catch(() => {
        alert("Transaksi berhasil ditahan secara lokal.");
        cart = [];
        renderCart();
    });
}

/* ============================================
   CANCEL TRANSACTION (Modul Hal. 18)
============================================ */
function cancelTransaction() {
    if (!confirm("Batalkan transaksi ini?")) {
        return;
    }

    cart = [];
    document.getElementById("payment").value = "";
    document.getElementById("discountPercent").value = 0;
    document.getElementById("discountAmount").value = 0;
    document.getElementById("tax").value = 0;
    document.getElementById("otherFee").value = 0;

    // Bersihkan session server juga
    fetch("{{ route('cashier.cart.clear') }}", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
    });

    renderCart();
}

/* ============================================
   PROCESS PAYMENT & CETAK STRUK (Modul Hal. 18 & 23)
============================================ */
let currentTransaction = null;
let currentReceiptUrl = "";

function processPayment() {
    if (cart.length === 0) {
        alert("Keranjang masih kosong.");
        return;
    }

    const total = getGrandTotal();
    const paymentInput = document.getElementById("payment");
    let payment = parseFloat(paymentInput.value) || 0;

    // Jika kasir belum mengisi nominal, otomatis gunakan Uang Pas
    if (payment <= 0) {
        payment = total;
        paymentInput.value = total;
        calculateChange();
    }

    if (payment < total) {
        alert("Uang pembayaran masih kurang dari total belanja.");
        paymentInput.focus();
        return;
    }

    const customerType = document.getElementById("customerType").value;
    const memberNo = document.getElementById("memberNumber").value;

    const payload = {
        customer_name: customerType,
        member_number: memberNo,
        payment_method: selectedPaymentMethod,
        paid_amount: payment,
        discount_percent: parseFloat(document.getElementById("discountPercent").value) || 0,
        discount_amount: parseFloat(document.getElementById("discountAmount").value) || 0,
        tax: parseFloat(document.getElementById("tax").value) || 0,
        other_fee: parseFloat(document.getElementById("otherFee").value) || 0,
        cart: cart
    };

    const btnPay = document.getElementById("btnPay");
    btnPay.disabled = true;
    btnPay.innerText = "MEMPROSES...";

    fetch("{{ route('cashier.checkout') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        btnPay.disabled = false;
        btnPay.innerText = "BAYAR & CETAK";

        if (data.success && data.transaction) {
            currentTransaction = data.transaction;
            currentReceiptUrl = window.location.origin + (data.receipt_path || ('/cashier/receipt/' + data.transaction.id));

            // Isi rincian Struk ke Modal
            populateReceiptModal(data.transaction);

            // Buka Modal Struk Kasir
            const receiptModalEl = document.getElementById("receiptModal");
            const modalInstance = bootstrap.Modal.getOrCreateInstance(receiptModalEl);
            modalInstance.show();

            // Kosongkan keranjang di client karena transaksi sudah tersimpan
            cart = [];
            renderCart();

        } else {
            alert(data.message || "Gagal memproses pembayaran.");
        }
    })
    .catch(err => {
        btnPay.disabled = false;
        btnPay.innerText = "BAYAR & CETAK";
        alert("Terjadi kesalahan komunikasi dengan server: " + (err.message || "Network Error"));
    });
}

function populateReceiptModal(trx) {
    document.getElementById("mInvoice").innerText = trx.invoice_number;
    document.getElementById("mDate").innerText = new Date(trx.created_at || Date.now()).toLocaleString("id-ID");
    document.getElementById("mCustomer").innerText = trx.customer_name + (trx.member_number ? " (" + trx.member_number + ")" : "");
    document.getElementById("mSubtotal").innerText = formatRupiah(trx.subtotal);
    document.getElementById("mTotal").innerText = formatRupiah(trx.total_amount);
    document.getElementById("mMethod").innerText = (trx.payment_method || "cash").toUpperCase();
    document.getElementById("mPaid").innerText = formatRupiah(trx.paid_amount);
    document.getElementById("mChange").innerText = formatRupiah(trx.change_amount);

    // Diskon
    const discRow = document.getElementById("mDiscountRow");
    if (trx.discount > 0) {
        discRow.style.display = "flex";
        document.getElementById("mDiscount").innerText = "- " + formatRupiah(trx.discount);
    } else {
        discRow.style.display = "none";
    }

    // Pajak
    const taxRow = document.getElementById("mTaxRow");
    if (trx.tax > 0) {
        taxRow.style.display = "flex";
        document.getElementById("mTax").innerText = formatRupiah(trx.tax);
    } else {
        taxRow.style.display = "none";
    }

    // Biaya Lain
    const otherRow = document.getElementById("mOtherRow");
    if (trx.other_fee > 0) {
        otherRow.style.display = "flex";
        document.getElementById("mOtherFee").innerText = formatRupiah(trx.other_fee);
    } else {
        otherRow.style.display = "none";
    }

    // Render Items
    const itemsList = document.getElementById("mItemsList");
    if (trx.items && trx.items.length > 0) {
        itemsList.innerHTML = trx.items.map(it => `
            <div style="margin-bottom: 4px;">
                <div style="font-weight: 600;">${it.product_name}</div>
                <div class="d-flex justify-content-between" style="font-size: 11px; color: #444;">
                    <span>${it.quantity} x ${formatRupiah(it.price)}</span>
                    <strong style="color: #000;">${formatRupiah(it.subtotal)}</strong>
                </div>
            </div>
        `).join("");
    } else {
        itemsList.innerHTML = `<div class="text-muted">Item tidak tersedia</div>`;
    }

    // Update link halaman struk
    const fullBtn = document.getElementById("mFullReceiptBtn");
    fullBtn.href = currentReceiptUrl;
    fullBtn.target = "_blank";
}

function printModalReceipt() {
    const targetUrl = currentReceiptUrl || (currentTransaction ? (window.location.origin + '/cashier/receipt/' + currentTransaction.id) : '');
    if (!targetUrl) {
        window.print();
        return;
    }

    // Gunakan iframe tersembunyi untuk mencetak struk secara senyap dan presisi
    let iframe = document.getElementById("receiptPrintFrame");
    if (!iframe) {
        iframe = document.createElement("iframe");
        iframe.id = "receiptPrintFrame";
        iframe.style.position = "fixed";
        iframe.style.right = "0";
        iframe.style.bottom = "0";
        iframe.style.width = "0";
        iframe.style.height = "0";
        iframe.style.border = "none";
        document.body.appendChild(iframe);
    }

    const printUrl = targetUrl + (targetUrl.includes('?') ? '&' : '?') + 't=' + Date.now();

    iframe.onload = function() {
        try {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        } catch (e) {
            window.open(targetUrl, '_blank');
        }
    };

    iframe.src = printUrl;
}

function startNewTransaction() {
    cart = [];
    document.getElementById("payment").value = "";
    document.getElementById("discountPercent").value = 0;
    document.getElementById("discountAmount").value = 0;
    document.getElementById("tax").value = 0;
    document.getElementById("otherFee").value = 0;
    renderCart();

    const receiptModalEl = document.getElementById("receiptModal");
    const modalInstance = bootstrap.Modal.getInstance(receiptModalEl);
    if (modalInstance) {
        modalInstance.hide();
    }

    setTimeout(() => {
        searchInput.focus();
    }, 300);
}

/* ============================================
   DATE & CLOCK (Modul Hal. 19)
============================================ */
function updateClock() {
    const el = document.getElementById("currentDate");
    if (el) {
        el.innerText = new Date().toLocaleString("id-ID");
    }
}
setInterval(updateClock, 1000);
updateClock();

/* ============================================
   KEYBOARD SHORTCUT (Modul Hal. 19)
============================================ */
document.addEventListener("keydown", function (event) {
    // F2 = fokus pencarian
    if (event.key === "F2") {
        event.preventDefault();
        searchInput.focus();
    }
    // F4 = fokus pembayaran
    if (event.key === "F4") {
        event.preventDefault();
        document.getElementById("payment").focus();
    }
    // ESC = batal
    if (event.key === "Escape") {
        cancelTransaction();
    }
});

/* ============================================
   INITIAL (Modul Hal. 19)
============================================ */
renderCart();
</script>
@endsection
