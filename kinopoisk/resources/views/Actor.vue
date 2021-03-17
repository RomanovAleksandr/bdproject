<template>
    <div class="home text-center py-5 mt-5" >
        <div class="container d-flex justify-content-center" v-if="!loading && !not_found">
            <div class="card" style="width: 18rem;">
                <img :src="actor.photo" class="card-img-top" alt="...">
                <div class="card-body">
                    <h5 class="card-title">{{actor.name}}</h5>
                    <p class="card-text">{{actor.birthday}}</p>
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
            <h3>404 actor not found</h3>
        </div>
    </div>
</template>

<script>
export default {
    data: () => ({
        loading: true,
        actor: null,
        films: null,
        not_found: false
    }),
    mounted() {
        this.loadActor(this.$route.params.id);
    },
    methods: {
        loadActor(id) {
            axios.get('/api/actors/' + id)
            .then(res => {
                this.actor = res.data.actor;
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