<template>
    <div class="home text-center py-5 mt-5" >
        <div class="container d-flex justify-content-center" v-if="!loading && !not_found">
            <div class="card" style="width: 18rem;">
                <img :src="director.photo" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">{{director.name}}</h5>
                    <p class="card-text">{{director.birthday}}</p>
                </div>
            </div>
            <table class="table" style="width: 30%">
                <thead>
                    <tr>
                    <th scope="col">Films</th>
                    </tr>
                </thead>
                <tbody>
                    <tr scope="row" v-for="film in films" v-bind:key="film.id" >
                        <router-link class="text-dark text-decoration-none" exact-active-class="false" :to="{ name: 'Film', params: { id: film.id } }">
                            <td>{{film.title}}</td>
                        </router-link>
                    </tr>
                </tbody>
            </table>
        </div>
        <div uk-alert v-if="not_found">
            <a class="uk-alert-close" uk-close></a>
            <h3>404 director not found</h3>
        </div>
    </div>
</template>

<script>
export default {
    data: () => ({
        loading: true,
        director: null,
        films: null,
        not_found: false
    }),
    mounted() {
        this.loadDirector(this.$route.params.id);
    },
    methods: {
        loadDirector(id) {
            axios.get('/api/directors/' + id)
            .then(res => {
                this.director = res.data.director;
                this.films = res.data.films;
                this.loading = false;
            })
            .catch(err => {
                this.not_found = true;
            })
        }
    }
}
</script>