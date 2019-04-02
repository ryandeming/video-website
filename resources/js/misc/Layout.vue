<template>
    <div>
        <header class="banner">
            <ul>
                <li><a href="/">AnimeWebsite</a></li>
                <li><a href="/series">View Anime</a></li>
                <li>
                    <form class="search" @submit.prevent="submit">
                        <input type="text" v-model="search.name">
                        <button type="submit">Search</button>
                    </form>
                </li>
            </ul>
            <div class="user-bar">
                <div class="user-menu" v-if="user">
                    <a :href="`/profile/${user.id}/${user.username}`" v-if="user">{{ user.username }} <i class="fas fa-caret-down" style="margin-left: 4px;"></i></a>
                    <ul class="dropdown">
                        <li>
                            <a :href="`/profile/${user.id}/${user.username}`" v-if="user">Profile</a>
                        </li>
                        <li>
                        <a href="/logout" v-if="user" @click.prevent="logout()">Logout</a>
                        </li>
                    </ul>
                </div>
                <div class="user-menu" v-if="!user">
                    <a href="/register">Register</a> <span style="margin: 0 5px;">|</span> 
                    <a href="/login">Login</a>
                </div>
                
            </div>
        </header>
        <div class="container">
            <main>
                <slot></slot>
            </main>
        </div>
    </div>
</template>

<style scoped>
    button[type=submit] {
        background: #282828;
        color: #b9b9b9;
        padding: 4px 13px;
        border: 1px solid #555;
    }
    input {
        background: rgba(255,255,255,.2);
        outline: 0;
        color: #b9b9b9;
        padding: 4px;
        border: 1px solid #666;
    }
</style>

<script>
import axios from 'axios';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
export default {
    props: ['title', 'user'],
    mounted() {
        this.updatePageTitle(this.title)
    },
    data() {
        return {
            search: {
                name: '',
            }
        }
    },
    watch: {
        title(title) {
            this.updatePageTitle(title)
        }
    },
    methods: {
        updatePageTitle(title) {
            document.title = title ? `${title} | Example Video App` : `Example Video App`
        },
        submit() {
            if(this.search.name !== '') {
                Turbolinks.visit(`/series/search/${this.search.name}`);
            }
        },
        logout() {
          axios.post(`/logout`).then(response => {
          window.location.href = '/'
          console.log(response);
        }).catch((error) => {
          console.log(error);
        });;
        }
    },
}
</script>