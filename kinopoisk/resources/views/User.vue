<template>
  <div class="home col-8 mx-auto py-5 mt-5">
    <h1>User</h1>
    <div class="card">
      <div class="card-body" v-if="user">
        <h3>{{ user.name }}</h3>
        <span>{{ user.email }}</span>
        <div>
           <img v-if="user.image_path" :src="'images/' + user.image_path" width="350" height="250">
        </div>
        <div>
            <input type="file" @change="onFileSelected">
            <button @click="onUpload">Upload</button>
            <button @click="deleteImage">Delete</button>
        </div>      
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      user: null,
      input_img: null,
    };
  },
  methods: {
      onFileSelected(event)
      {
        this.input_img = event.target.files[0]
      },
      onUpload()
      {
        const fd = new FormData();
        fd.append('input_img', this.input_img, this.input_img.name)
        axios.post('api/upload-image', fd)
          .then(Response => {
              this.user.image_path = Response.data.path
          })
      },
      deleteImage()
      {
        axios.get('api/delete-image')
          .then(Response => {
              this.user.image_path = null;
          })
      }
  },
  mounted() {
    axios.get('/api/user').then(Response =>{
        this.user = Response.data;
    });
  }
};
</script>