# eStore-Template
# 🛒 Grizm Store - Modern E-Commerce System

Grizm Store, modern tasarımı ve gelişmiş özellikleriyle dikkat çeken, PHP ile geliştirilmiş minimal bir e-ticaret platformudur. Bu sistem, standart satışların yanı sıra kişiye özel **"Tek Kullanımlık Satın Alım Linkleri"** oluşturmanıza olanak tanır.



## ✨ Öne Çıkan Özellikler

* **💎 VIP Satış Sistemi:** Admin panelinden tek alımlık ürünler oluşturun ve müşterinize özel bir gizli link (`token`) gönderin.
* **🚫 Otomatik Stok Yönetimi:** Tek alımlık ürünler satıldığı anda sistem tarafından otomatik olarak satışa kapatılır ve link geçersiz kılınır.
* **📲 Telegram Entegrasyonu:** Her yeni siparişte, sipariş detayları (Müşteri adı, tutar, adres ve ürünler) anlık olarak Telegram üzerinden adminin telefonuna düşer.
* **🎨 Modern UI/UX:** "Plus Jakarta Sans" fontu ve "Dark Mode" odaklı, premium bir arayüz tasarımı.
* **🔒 Güvenlik:** Admin paneli yetkilendirme sistemi ve güvenli resim yükleme (upload) altyapısı.

## 🛠️ Teknik Detaylar

* **Backend:** PHP (PDO)
* **Frontend:** Bootstrap 5, Custom CSS3, FontAwesome 6
* **Veritabanı:** MySQL
* **Bildirim:** Telegram Bot API



## 🚀 Kurulum

1.  Bu depoyu bilgisayarınıza klonlayın:
    ```bash
    git clone [https://github.com/kullaniciadi/grizm-store.git](https://github.com/yusufber/grizm-store.git)
    ```
2.  `db.php` dosyasını kendi veritabanı bilgilerinizle güncelleyin.
3.  `sql/setup.sql` dosyasındaki kodları phpMyAdmin üzerinden çalıştırarak tabloları oluşturun.
4.  Telegram bildirimleri için `functions.php` (veya ilgili bildirim fonksiyonu) içindeki **Bot Token** ve **Chat ID** alanlarını doldurun.

## 📂 Veritabanı Yapısı

Sistemin hatasız çalışması için aşağıdaki tablolar otomatik olarak kurulur:
* `users`: Admin ve müşteri kayıtları.
* `products`: Genel ve VIP ürün listesi.
* `orders`: Siparişlerin genel bilgileri.
* `order_items`: Sipariş edilen ürünlerin detayları.

## 📝 Lisans
Bu proje eğitim ve kişisel kullanım amacıyla geliştirilmiştir.
