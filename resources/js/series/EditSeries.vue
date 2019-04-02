
<template>
    <layout :title="series.name" :user="user">
      <h1>EDIT {{ series.name }}</h1>
      <form @submit.prevent="submit">
        <div class="form-group">
          <label>Name:</label>
          <input class="form-control" type="text" v-model="form.name">
          <div class="text-danger" v-if="errors.has('name')">
              {{ errors.first('name') }}
          </div>
        </div>
        <button type="submit">Save Changes</button>
      </form>
    </layout>
</template>

<style>
    #video {
        background: #000;
        margin-bottom: 1rem;
    }
</style>

<script>
import axios from 'axios'
import { Errors } from 'form-backend-validation';

export default {
    props: ['series'],
    data() {
      return {
        form: {
          name: this.series.name,
        },
        errors: new Errors(),
      }
    },
    methods: {
      submit() {
        axios.put(`/series/${this.series.id}`, this.form).then(response => {
          Turbolinks.visit(`/series/${this.series.id}`)
          console.log(response);
        }).catch((error) => {
          //this.errors = new Errors(error.response.data.errors);
          console.log(error);
        });
      },
    }
}
</script>