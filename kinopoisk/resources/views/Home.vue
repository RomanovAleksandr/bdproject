<template>
	<div class="home text-center py-5 mt-5">
    <h2 v-if="user">{{ user.name }}, {{ user.email }}</h2>
    <div class="container d-flex">
		<div 
				v-for="film in films" 
				v-bind:key="film.id" 
				class="text-dark text-decoration-none">
			<div class="card" style="width: 18rem;">
				<img :src="film.poster" class="card-img-top" alt="...">
				<div class="card-body">
					<router-link
						exact-active-class="false" 
						:to="{ name: 'Film', params: { id: film.id } }">
						<h5 class="card-title">{{film.title}}</h5>
					</router-link>
					<p class="card-text">{{film.year}}</p>
					<!-- <router-link 
						v-for="genre in genres" 
						v-bind:key="genre.id + genre.film_id" 
						class="text-dark text-decoration-none" 
						exact-active-class="true" 
						:to="{ name: 'Home', params: { genre_id: genre.id }}">
						<p v-if="genre.film_id === film.id" class="card-text">{{genre.genre}}</p>
					</router-link> -->
					<div v-for="genre in genres" v-bind:key="genre.id + genre.film_id">
						<p v-if="genre.film_id === film.id" v-on:click="getGenre(genre.id)">{{genre.genre}} </p>
					</div>
				</div>
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
		films: [],
		genres: []
		};
	},
	watch: {
       // call the method if the route changes
       '$route': {
         handler: 'loadFilm',
         immediate: true // runs immediately with mount() instead of calling method on mount hook
       }
    },
	mounted() {
		if (!!localStorage.getItem("auth")) {
			axios.get('/api/user').then(Response =>{
				this.user = Response.data;
			})
		}
		//this.loadFilm();	
	},
	methods: {
		loadFilm() {
			axios.get('/api/films', {
				params: this.$route.query
				})
				.then(res => {
				this.films = res.data.films;
				this.genres = res.data.genres;
				})
			},
		getGenre(genre_id) {
			this.$router.replace({ query: { 'genre_id': genre_id } }).catch(()=>{});
		}
		}
	};
</script>