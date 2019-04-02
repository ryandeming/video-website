
<template>
    <layout title="Register">
      <form @submit.prevent="doRegister">
        <h1 class="text-primary"><span class="underline">Register</span></h1>
        <div class="form-group row">
            <label for="username" class="col-md-4 col-form-label text-md-right">Username</label>
            <input v-model="register.username" id="username" type="text" class="form-control" name="username" value="" required autofocus>
        </div>
        <span v-if="errors.errors" class="invalid-feedback" role="alert">
          <strong v-for="error in errors.errors.username" :key="error">{{ error }}</strong>
        </span>

        <div class="form-group row">
          <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>
          <input v-model="register.email" id="email" type="email" class="form-control" name="email" required>
        </div>
        <span v-if="errors.errors" class="invalid-feedback" role="alert">
          <strong v-for="error in errors.errors.email" :key="error">{{ error }}</strong>
        </span>

        <div class="form-group row">
          <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
          <input v-model="register.password" id="password" type="password" class="form-control" name="password" required>
        </div>

        <div class="form-group row">
          <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirm Password</label>
          <input v-model="register.password_confirmation" id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
        </div>
        <span v-if="errors.errors" class="invalid-feedback" role="alert">
          <strong v-for="error in errors.errors.password" :key="error">{{ error }}</strong>
        </span>

        <button type="submit">Register</button>
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
        register: {
          username: '',
          email: '',
          password: '',
          password_confirmation: '',
        },
        errors: new Errors(),
      }
    },
    methods: {
      doRegister() {
        axios.post(`/register`, this.register).then(response => {
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