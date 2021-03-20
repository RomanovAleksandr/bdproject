<template>
    <div class="home text-center py-5 mt-5">
        <div class="container d-flex justify-content-center" v-if="!loading && !not_found">
            <div class="card" style="width: 18rem;">
                <img :src="film.poster" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">{{film.title}}</h5>
                        <h5 class="card-title">{{film.year}}</h5>
                        <p class="card-text">{{film.description}}</p>
                        <p class="card-text">duration {{film.duration}}</p>
                        <p class="card-text">score {{score}}</p>
                        <star-rating @rating-selected ="setRating" v-bind:read-only="!user ? true : false" v-model="rating"></star-rating>
                    </div>
                </div>
            <div class="d-flex flex-column" style="width: 30%">
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">Actors</th>
                    </tr>
                </thead>
                <tbody>
                    <tr scope="row" v-for="actor in actors" v-bind:key="actor.id" >
                        <router-link class="text-decoration-none" exact-active-class="false" :to="{ name: 'Actor', params: { id: actor.id } }">
                            <td>{{actor.name}}</td>
                        </router-link>   
                    </tr>
                </tbody>
            </table>
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">Directors</th>
                    </tr>
                </thead>
                <tbody>
                    <tr scope="row" v-for="director in directors" v-bind:key="director.id" >
                        <router-link class="text-decoration-none" exact-active-class="false" :to="{ name: 'Director', params: { id: director.id } }">
                            <td>{{director.name}}</td>
                        </router-link>   
                    </tr>
                </tbody>
            </table>
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">Genres</th>
                    </tr>
                </thead>
                <tbody>
                    <tr scope="row" v-for="genre in genres" v-bind:key="genre.id" >
                        <router-link class="text-decoration-none" exact-active-class="false" :to="{ name: 'Home', query: { 'genre_id': genre.id } }">
                            <td>{{genre.genre}}</td>
                        </router-link>   
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
        <div class="container" style="width: 50%">
            <form @submit.prevent="addComment">
                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label">Add comment</label>
                    <textarea class="form-control" v-model="newComment.comment" rows="3" :disabled="!user ? true : false"></textarea>
                    <span class="text-danger" v-if="errors.comment">
                        {{ errors.comment[0] }}
                    </span>
                </div>
                <button type="submit" class="btn btn-primary" :disabled="!user ? true : false">Submit</button>
            </form>
            <button type="button" class="btn btn-primary" v-on:click="loadComments">Reverse</button>
            <ul class="list-group">
                    <li class="list-group-item" v-for="comment, index in comments" :key="comment.id">
                        <div class="row">
                            <div class="col-xs-2 col-md-1">
                                <img :src="'../images/' + comment.image_path" class="rounded-circle" alt="" width="120" height="120" />
                            </div>
                            <div class="col-xs-10 col-md-11">
                                <div>
                                    <div>
                                        <p>{{comment.name}}</p>
                                        <p>created at: {{comment.created_at}}</p>
                                        <p v-if="comment.created_at != comment.updated_at">updated at: {{comment.updated_at}}</p> 
                                    </div>
                                </div>
                                <div class="comment-text" style="white-space: pre-line">
                                    {{comment.comment}}
                                </div>
                                <div v-if="user && user.id == comment.user_id" class="action">
                                    <button class="btn btn-primary mb-2" @click="showModal(comment)">Edit</button>
                                    <button class="btn btn-danger mb-2" v-on:click="deleteComment(comment.id, index)">Delete</button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
        </div>
        <div uk-alert v-if="not_found">
            <a class="uk-alert-close" uk-close></a>
            <h3>404 film not found</h3>
        </div>
        <modal name="hello-world">
            <div class="wrapper">
                <form @submit.prevent="editComment">
                        <div class="mb-3">  
                            <textarea class="form-control" v-model="commentToEdit.comment" rows="7" :disabled="!user ? true : false"></textarea>
                            <span class="text-danger" v-if="errors.comment">
                                {{ errors.comment[0] }}
                            </span>
                        </div>
                        <button type="submit" class="btn btn-primary" :disabled="!user ? true : false">Submit</button>
                        <button type="button" class="btn btn-danger" v-on:click="closeModal" :disabled="!user ? true : false">Cancel</button>
                </form>
            </div>
        </modal>
    </div>
</template>

<script>
import StarRating from 'vue-star-rating'
export default {
    data: () => ({
        loading: true,
        film: null,
        actors: null,
        directors: null,
        genres: null,
        not_found: false,
        user: null,
        newComment: {},
        comments: [],
        errors: [],
        commentToEdit: {},
        score: null,
        rating: null,
        reverseComments: false
    }),
    components: {
        StarRating
    },
    computed: {
      itemToShow: function () {
        return this.item
      }
    },
    mounted() {
        if (!!localStorage.getItem("auth")) {
			axios.get('/api/user').then(Response =>{
				this.user = Response.data;
                axios.get('/api/scores/' + this.$route.params.id).then(Response =>{
                    this.rating = Response.data;
                })
			})
		}
        this.loadFilm(this.$route.params.id);
        this.loadComments();
    },
    methods: {
        loadFilm(id) {
            axios.get('/api/films/' + id)
            .then(res => {
                this.film = res.data.film;
                this.actors = res.data.actors;
                this.directors = res.data.directors;
                this.genres = res.data.genres;
                //this.comments = res.data.comments;
                this.score = Math.floor(res.data.score * 10) / 10;
                this.loading = false;
            })
            .catch(err => {
                this.not_found = true;
            })
        },
        loadComments()
        {
            axios.get('/api/comments', {
                params: {
                    film_id: this.$route.params.id,
                    reverse: this.reverseComments
                }
                }).then(Response =>{
                this.comments = Response.data;
            })
            this.reverseComments = !this.reverseComments
        },
        addComment() {
            this.newComment.film_id = this.film.id;
            axios.post('/api/comments/', this.newComment)
            .then(res => {
                res.data["image_path"] = this.user.image_path;
                this.comments = [res.data].concat(this.comments)
                this.errors = []
            })
            .catch(error => {
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors;
                }
            });
            this.newComment.comment = '';
        },
        editComment() {
            axios.patch('/api/comments/'+ this.commentToEdit.id, this.commentToEdit)
            .then(res => {
                
            })
            this.$modal.hide('hello-world');
        },
        deleteComment(id, index) {
            axios.delete('/api/comments/'+ id)
            .then(res => {
                this.comments.splice(index, 1);
            })
        },
        showModal(comment) {
            this.commentToEdit = comment;
            this.$modal.show('hello-world');
        },
        closeModal() {
            this.$modal.hide('hello-world');
        },
        setRating (rating) {
            this.rating = rating;
            axios.post('/api/scores/', {'film_id': this.film.id, 'score': this.rating})
            .then(res => {
                this.score = res.data;
            })
        },
    }
}
</script>