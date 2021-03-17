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
                        <router-link class="text-dark text-decoration-none" exact-active-class="false" :to="{ name: 'Actor', params: { id: actor.id } }">
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
                        <router-link class="text-dark text-decoration-none" exact-active-class="false" :to="{ name: 'Director', params: { id: director.id } }">
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
                        <router-link class="text-dark text-decoration-none" exact-active-class="false" :to="{ name: 'Home', query: { 'genre_id': genre.id } }">
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
            <ul class="list-group">
                    <li class="list-group-item" v-for="comment, index in comments" :key="comment.id">
                        <div class="row">
                            <div class="col-xs-2 col-md-1">
                                <img :src="'../images/' + user.image_path" class="rounded-circle" alt="" width="120" height="120" />
                            </div>
                            <div class="col-xs-10 col-md-11">
                                <div>
                                    <div>
                                        <p>{{comment.name}}</p> {{comment.created_at}}
                                    </div>
                                </div>
                                <div class="comment-text">
                                    {{comment.comment}}
                                </div>
                                <div class="action">
                                    <!-- <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#exampleModal" @click="onClick">Edit</button> -->
                                    <button class="btn btn-primary mb-2" @click="onClick">Edit</button>
                                    <button class="btn btn-danger mb-2" v-on:click="deleteComment(comment.id, index)">Delete</button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
        </div>
        <!-- <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit comment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    <form @submit.prevent="addComment">
                        <div class="mb-3">  
                            <textarea class="form-control" v-model="newComment.comment" rows="3" :disabled="!user ? true : false"></textarea>
                            <span class="text-danger" v-if="errors.comment">
                                {{ errors.comment[0] }}
                            </span>
                        </div>
                        <button type="submit" class="btn btn-primary" :disabled="!user ? true : false">Submit</button>
                    </form>
                    </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                </div>
            </div>
        </div> -->
        <div uk-alert v-if="not_found">
            <a class="uk-alert-close" uk-close></a>
            <h3>404 film not found</h3>
        </div>
        <modal name="hello-world" @before-open="beforeOpen">
            <div class="wrapper">
                <form @submit.prevent="editComment">
                        <div class="mb-3">  
                            <textarea class="form-control" v-model="newComment.comment" rows="3" :disabled="!user ? true : false"></textarea>
                            <span class="text-danger" v-if="errors.comment">
                                {{ errors.comment[0] }}
                            </span>
                        </div>
                        <button type="submit" class="btn btn-primary" :disabled="!user ? true : false">Submit</button>
                    </form>
            </div>
        </modal>
    </div>
</template>

<script>
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
        item: ''
    }),
    computed: {
      itemToShow: function () {
        return this.item
      }
    },
    mounted() {
        if (!!localStorage.getItem("auth")) {
			axios.get('/api/user').then(Response =>{
				this.user = Response.data;
			})
		}
        this.loadFilm(this.$route.params.id);
    },
    methods: {
        loadFilm(id) {
            axios.get('/api/films/' + id)
            .then(res => {
                this.film = res.data.film;
                this.actors = res.data.actors;
                this.directors = res.data.directors;
                this.genres = res.data.genres;
                this.comments = res.data.comments;
                this.loading = false;
            })
            .catch(err => {
                this.not_found = true;
            })
        },
        addComment() {
            this.newComment.film_id = this.film.id;
            axios.post('/api/comments/', this.newComment)
            .then(res => {
                //this.comments.push(res.data);
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

        },
        deleteComment(id, index) {
            console.log(id, index);
            axios.delete('/api/comments/'+ id)
            .then(res => {
                this.comments.splice(index, 1);
                console.log(res.data)
            })
        },
        beforeOpen (event) {
        this.item = event.params.item;
      },
      onClick() {
        this.$modal.show('hello-world', {item: 'Hello world'});
      }
    }
}
</script>