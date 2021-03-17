import Vue from "vue";
import VueRouter from "vue-router";

import Home from "../views/Home.vue";
import Login from "../views/Login.vue";
import Register from "../views/Register.vue";
import User from "../views/User.vue";
import Film from "../views/Film.vue";
import Actor from "../views/Actor.vue";
import Director from "../views/Director.vue";

Vue.use(VueRouter);

const routes = [
    {
      path: "/",
      name: "Home",
      component: Home
    },
    {
      path: "/login",
      name: "Login",
      component: Login,
      meta: { guestOnly: true }
    },
    {
      path: "/register",
      name: "Register",
      component: Register,
      meta: { guestOnly: true }
    },
    {
      path: "/user",
      name: "User",
      component: User,
      meta: { authOnly: true }
    },
    {
      path: "/films/:id",
      name: "Film",
      component: Film
    },
    {
      path: "/actors/:id",
      name: "Actor",
      component: Actor
    },
    {
      path: "/directors/:id",
      name: "Director",
      component: Director
    },
  ];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes
});

function isLoggedIn() {
  return localStorage.getItem("auth");
}

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.authOnly)) {
    // this route requires auth, check if logged in
    // if not, redirect to login page.
    if (!isLoggedIn()) {
      next({
        path: "/login",
        query: { redirect: to.fullPath }
      });
    } else {
      next();
    }
  } else if (to.matched.some(record => record.meta.guestOnly)) {
    // this route requires auth, check if logged in
    // if not, redirect to login page.
    if (isLoggedIn()) {
      next({
        path: "/user",
        query: { redirect: to.fullPath }
      });
    } else {
      next();
    }
  } else {
    next(); // make sure to always call next()!
  }
});

export default router;