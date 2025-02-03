<template>
  <div class="container">
    <h2>إرسال عقد التوقيع</h2>
    <button @click="sendContract">إرسال العقد</button>
    <p v-if="message">{{ message }}</p>

    <button @click="sendEmbeddedContract">التحويل للعقد</button>
  </div>


</template>




<script>
import axios from "axios";

export default {
  data() {
    return {
      message: "",
    };
  },
  methods: {
 async sendContract() {
  try {
    const response = await axios.post(
      "http://127.0.0.1:8000/send-contract",
      {
        recipient_name: "Mohamed Ahmed",
        recipient_email: "ebrahim.hosny@hadaf-hq.com",
      },
      {
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
      }
    );

    this.message = "تم إرسال العقد بنجاح!";
    console.log("تم إرسال العقد:", response.data);
  } catch (error) {
    this.message = "حدث خطأ أثناء إرسال العقد.";
    console.error("خطأ:", error);
  }
},

 async sendEmbeddedContract() {
      try {
        const recipientName = "أحمد علي";
        const recipientEmail = "ahmed@example.com";

        const response = await axios.post('/send-embedded-contract', {
          recipient_name: recipientName,
          recipient_email: recipientEmail,
        });

        if (response.data && response.data.sign_url) {
          window.location.href = response.data.sign_url; 
        } else {
          alert("حدث خطأ أثناء معالجة الطلب.");
        }
      } catch (error) {
        console.error("خطأ في الاتصال: ", error);

        alert("حدث خطأ أثناء إرسال العقد.");
      }
    }
  

  },
};
</script>

<style scoped>
.container {
  width: 50%;
  margin: auto;
  text-align: center;
  padding: 20px;
}

button {
  padding: 10px 20px;
  background-color: blue;
  color: white;
  border: none;
  cursor: pointer;
  font-size: 16px;
}

button:hover {
  background-color: darkblue;
}
</style>
