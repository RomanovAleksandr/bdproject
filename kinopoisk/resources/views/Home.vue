<template>
	<div class="home py-5 mt-5">
    <div class="container">
		<div>
			<label>genre</label>
			<select class="form-select" aria-label="Default select example" v-model="searchGenre">
				<option value="all">all</option>
				<option v-for="genre in genresSelect" :value="genre.id">{{genre.genre}}</option>
			</select>
			<label>year</label>
     		<select class="form-select" aria-label="Default select example" v-model="searchYear">
				 <option value="all">all</option>
				<option v-for="year in years" :value="year">{{ year }}</option>
			</select>
			<div class="input-group">
				<input type="search" class="form-control rounded" placeholder="Search" aria-label="Search"
					aria-describedby="search-addon" v-model="searchWord" />
				<button type="button" class="btn btn-outline-primary" v-on:click="searchByParameter">search</button>
			</div>
		</div>
		<div class="d-flex">
		<div 
				v-for="film in films" 
				v-bind:key="film.id" 
				class="text-decoration-none">
			<div class="card text-center" style="width: 18rem;">
				<img :src="film.poster" class="card-img-top" alt="...">
				<div class="card-body">
					<router-link
						exact-active-class="false" 
						:to="{ name: 'Film', params: { id: film.id } }">
						<h5 class="card-title">{{film.title}}</h5>
					</router-link>
					<p class="card-text">{{film.year}}</p>
					<div class="d-flex justify-content-around">
						<div v-for="genre in genres" v-bind:key="genre.id + genre.film_id">
							<button class="btn btn-danger" v-if="genre.film_id === film.id" v-on:click="getGenre(genre.id)">{{genre.genre}}</button>
						</div>
					</div>
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
		genres: [],
		genresSelect: [],
		searchYear: 'all',
		searchGenre: 'all',
		searchWord: '',
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
	},
	computed : {
		years () {
			const year = new Date().getFullYear()
			return Array.from({length: year - 1900}, (value, index) => 1901 + index)
		},
	},
	methods: {
		loadFilm() {
			axios.get('/api/films', {
				params: this.$route.query
				})
				.then(res => {
				this.films = res.data.films;
				this.genres = res.data.genres;
				this.genresSelect = res.data.allGenres
				})
			},
		getGenre(genre_id) {
			this.$router.replace({ query: { 'genre_id': genre_id } }).catch(()=>{});
		},
		searchByParameter()
		{
			this.$router.replace({ query: { 'genre_id': this.searchGenre, 'year': this.searchYear, 'search': this.searchWord } }).catch(()=>{});
		},
		}
	};
</script>