<template>
  <div>
    <h3>رفع ملف الإكسل</h3>
    <input type="file" @change="uploadFile" />
  </div>
</template>

<script>
import axios from 'axios';

export default {
  methods: {
    async uploadFile(event) {
      const file = event.target.files[0];
      const formData = new FormData();
      formData.append('file', file);

      const response = await axios.post('/api/upload-excel', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', 'results.xlsx');
      document.body.appendChild(link);
      link.click();
    },
  },
};
</script>
