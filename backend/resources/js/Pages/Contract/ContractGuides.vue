<template>
  <div class="container my-5">
    <h1 class="text-center mb-4">إرشادات توثيق عقد إلكتروني</h1>

    <!-- الخطوة 1 -->
    <div class="guide-section">
      <div class="d-flex align-items-center">
        <span class="step-number">1</span>
        <h3>الدخول للعقد</h3>
      </div>
      <p class="mt-2">
        انقر على زر <strong>"توثيق العقد"</strong> الموجود بصفحتك الشخصية
      </p>
        <!-- <img src="/img/Contract/start.png" alt="تسجيل الدخول"> -->
    </div>

    <!-- الخطوة 2 -->
    <div class="guide-section">
      <div class="d-flex align-items-center">
        <span class="step-number">2</span>
        <h3>تعديل اللغة الى العربية</h3>
      </div>
      <p class="mt-2">
        يمكنك اختيار اللغة العربية كما موضح في الصورة
      </p>
      <img src="/img/contract/change-language.png" alt="إنشاء عقد جديد">

    </div>

    <!-- الخطوة 3 -->
    <div class="guide-section">
      <div class="d-flex align-items-center">
        <span class="step-number">3</span>
        <h3>الموافقة والمتابعة</h3>
      </div>
      <p class="mt-2">
        قراءة العقد جيدًا، ثم الموافقة والمتابعة كما هو موضح بالصورة
      </p>
      <img src="/img/contract/confirm.png" alt="تحميل المستندات">
    </div>

    <!-- الخطوة 4 -->
    <div class="guide-section">
      <div class="d-flex align-items-center">
        <span class="step-number">4</span>
        <h3>التوقيع الإلكتروني</h3>
      </div>
      <p class="mt-2">
        اختر التوقيع بواسطة Emdaa ثم قم بالتوقيع كما هو موضح بالصور
      </p>
      <img src="img/contract/sign.png" alt="التوقيع الإلكتروني">
      <img src="img/contract/sign2.png" alt="التوقيع الإلكتروني">
    </div>

    <!-- الخطوة 5 -->
    <div class="guide-section">
      <div class="d-flex align-items-center">
        <span class="step-number">5</span>
        <h3>التحقق بواسطة نفاذ</h3>
      </div>
      <p class="mt-2 text-end">
        سيتم توجيهك الى نفاذ، اكتب رقم هويتك ثم انقر على الموافقة والمتابعة، ثم توقيع المستند
      </p>
      <p class="mt-2 text-end">
        افتح تطبيق نفاذ على جوالك ثم اختر الرقم المطلوب
      </p>
      <img  src="/img/contract/nafath.png" alt="إرسال العقد">
      <img class="text-center mx-auto" src="/img/contract/nafath-auth.png" alt="إرسال العقد">
    </div>

    <!-- الخطوة 6 -->
    <div class="guide-section">
      <div class="d-flex align-items-center">
        <span class="step-number">6</span>
        <h3>انتظار التوثيق</h3>
      </div>
      <p class="mt-2">
        لا تغلق الصفحة وانتظر حتى يتم التوثيق، قد تصل المدة إلى 40 ثانية، حتى تظهر الصورة المرفقة بأنه تم التوثيق
      </p>
      <img src="/img/contract/done.png" alt="إرسال العقد">
    </div>

    <!-- نهاية الإرشادات -->
    <div class="text-center mt-4">
      <p class="footer-text mt-3">إذا واجهتك أي مشكلة، يرجى التواصل مع الدعم الفني على الرقم: <strong>3268 133 055</strong></p>
    </div>
  </div>
</template>

<script>
export default {
  name: "ContractGuides",
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;700&display=swap');

body {
  font-family: 'Tajawal', sans-serif;
  background-color: #f5f5f5;
  color: #333;
  margin: 0;
  padding: 0;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.container {
  width: 100%;
  max-width: 1000px;
  padding: 30px;
  background: #ffffff;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  margin: 0 auto;
}

h1 {
  color: #1E3A8A;
  font-weight: bold;
  border-bottom: 3px solid #d4af37;
  display: inline-block;
  padding-bottom: 5px;
}

.guide-section {
  margin-bottom: 2.5rem;
  padding: 20px;
  border-radius: 8px;
  background: #f8f9fa;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.guide-section img {
  max-width: 100%;
  border-radius: 8px;
  border: 3px solid #ddd;
  margin-top: 1rem;
  transition: transform 0.3s ease-in-out;
}

.guide-section img:hover {
  transform: scale(1.03);
}

.step-number {
  font-size: 1.5rem;
  font-weight: bold;
  color: #d4af37;
  margin-left: 0.7rem;
}

.btn-custom {
  background-color: #1E3A8A;
  color: white;
  font-weight: bold;
  padding: 10px 20px;
  border-radius: 5px;
  transition: background 0.3s;
}

.btn-custom:hover {
  background-color: #d4af37;
  color: black;
}

.footer-text {
  color: #555;
  font-size: 14px;
}
</style>
