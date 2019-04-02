
<template>
    <layout title="Login">
      <form @submit.prevent="doLogin">
        <h1 class="text-primary"><span class="underline">Login</span></h1>
        <div class="form-group row">
          <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>
          <input id="email" type="email" name="email" value="" v-model="login.email" required autofocus>
        </div>
        <div class="form-group row">
          <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
          <input id="password" type="password" name="password" v-model="login.password" required>
        </div>
        <div class="form-group row">
          <label class="form-check-label" for="remember">Remember Me</label>
          <input class="form-check-input" type="checkbox" name="remember" id="remember" v-model="login.remember">
        </div>
        <button type="submit">Login</button>
        <span v-if="errors.errors" class="invalid-feedback" role="alert">
          <strong v-for="error in errors.errors.email" :key="error">{{ error }}</strong>
        </span>
      </form>
    </layout>
</template>

<style scoped>
  form {
    max-width: 420px;
    margin: 0 auto;
    display: block;
  }

  h1 {
    margin-bottom: 30px;
    display: block;
  }

  form button[type=submit] {
    float: right;
  }

  .form-group {
    margin: 15px 0 15px;
    display: flex;
  }

  .form-group label {
    width: 25%;
  }

  .form-group input {
    width: 75%
  }

  .form-group input[type="checkbox"] {
    width: auto;
  }
</style>

<script>
import axios from 'axios'
import { Errors } from 'form-backend-validation';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
export default {
    data() {
      return {
        login: {
          email: '',
          password: '',
          remember: true,
        },
        errors: new Errors(),
      }
    },
    methods: {
      doLogin() {
        axios.post(`/login`, this.login).then(response => {
          //Turbolinks.visit(`/login`)
          console.log(response);
          if(response.status == 200) {
            window.location.href = '/'
          }
        }).catch((error) => {
          this.errors = new Errors(error.response.data.errors);
          console.log(error);
        });
      },
    }
}
</script>