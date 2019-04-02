
<template>
    <layout :title="series.name" :user="user">
      <div class="series flex">
        <div class="secondary">
          <div class="poster" v-on:click="modalHidden = false">
            <img class="lazy" v-if="series.poster" :src="series.poster">
            <div class="overlay"></div>
            <div class="play">
              <i class="fas fa-play-circle"></i>
            </div>
          </div>
          <div class="series-info">
            <p>Total Episodes <span>{{ series.total_eps }}</span></p>
            <p>Type <span>{{ series.type }}</span></p>
            <p>Status <span>{{ series.status }}</span></p>
            <p>Aired <span>{{ formatDate(series.start_date) }} - {{ formatDate(series.end_date) }}</span></p>
          </div>
          <div v-if="!modalHidden" class="youtube modal" v-on:click="modalHidden = true">
            <div class="modal-inner">
              <div>
                <iframe :src="`https://www.youtube.com/embed/${series.youtube_trailer_id}`" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
              </div>
            </div>
          </div>
        </div>
        <div class="primary">
          <h1 class="white">{{ series.name }}</h1>
          <div v-if="series.genres != ''" class="genre-list flex">
            <div v-for="genre in series.genres" :key="genre">
              <a :href="`/series/genre/${genre}`">
                <div class="genre">
                  {{ genre }}
                </div>
              </a>
            </div>
          </div>
          <div v-if="user" class="library-info">
            <button class="add-to-library" v-if="!library" @click="addLibrary()">Add to Library</button>
            <div v-if="library" class="library">
              <select v-model="libraryData.status" @change="updateLibrary()">
                <option value="1">Watching</option>
                <option value="2">Completed</option>
                <option value="3">On Hold</option>
                <option value="4">Dropped</option>
                <option value="5">Plan to Watch</option>
              </select>

              <div class="episodes-watched" style="position: relative; display: inline-block;">
                <p style="position: absolute; right: 8px; top: 1px; pointer-events: none; font-size: 0.75rem;">Episodes Watched</p>
                <select v-model="libraryData.last_watched_episode" @change="updateLibrary()">
                  <option :value="null" disabled>Episodes Watched</option>
                  <option v-for="index in series.total_eps" :value="index" :key="index">{{ index }}</option> 
                </select>
              </div>
              <select v-model="libraryData.rating" @change="updateLibrary()">
                <option :value="null">Rating</option>
                <option v-for="index in 10" :value="index" :key="index">{{ index }}</option> 
              </select>
              <input type="checkbox" id="is_fav" name="is_fav" v-model="libraryData.is_fav" @change="updateLibrary()" style="opacity: 0; height: 0; width: 0;">
              <label for="is_fav"><i class="far fa-star" v-if="!libraryData.is_fav"></i><i class="fas fa-star" v-if="libraryData.is_fav"></i></label>
            </div>
          </div>
          <p class="justify">{{ series.description }}</p>
          <div class="videos-list flex flex-wrap">
            <div class="card video-card base-thirds" v-for="video in episodes.data" :key="video.id">
              <a :href="`/watch/${series.id}/${ series.name }/${ video.episode }`">
              <div class="thumbnail">
                <div class="overlay"></div>
                  <img class="lazy" :src="series.poster" :alt="series.name">
              </div>
                <p><span class="text-primary left">{{ series.name }}</span> <span class="right">EP {{ video.episode }}</span></p>
            </a>
            </div>
          </div>
          <div class="pagination">
            <a class="prev" data-turbolinks-scroll="false" v-if="episodes.current_page !== 1" :href="episodes.prev_page_url"><i class="fas fa-chevron-left"></i></a> <a class="next" data-turbolinks-scroll="false" v-if="episodes.current_page !== episodes.last_page" :href="episodes.next_page_url"><i class="fas fa-chevron-right"></i></a>
          </div>
        </div>
      </div>
    </layout>
</template>

<style scoped>
  .flex {
    flex-grow: 1;
  }

  .series-info p {
    text-transform: uppercase;
    font-size: 14px;
  }

  .series-info span {
    float: right;
    color: #fff;
  }

  i.fa-star {
    color: #FFBA08;
  }

  h1 {
    color: #fff;
    font-size: 32px;
    margin-bottom: 0.75rem;
  }

  .secondary {
    padding-right: 30px;
    max-width: 280px;
    box-sizing: border-box;
  }

  .primary {
    width: calc(100% - 300px);
    flex-grow: 1;
  }

  .poster {
    cursor: pointer;
    position: relative;
  }

  .poster .overlay {
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    background-color: #000;
    opacity: 0.5;
    transition: all 0.3s;
  }

  .poster:hover .overlay {
    opacity: 0.8;
  }

  .poster .play {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 48px;
    color: #fff;
    opacity: 0.5;
    transition: all 0.3s;
  }

  .poster:hover .play {
    opacity: 1;
  }

  .genre-list {
    justify-content: flex-start;
    margin: 1rem 0;
  }

  .genre {
    padding: 8px 14px;
    background: rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.5);
    margin: 0 10px 0 0;
    border-radius: 3px;
  }

  .library-info {
    margin: 16px 0;
  }

  .add-to-library {
    display: inline-block;
    margin: 0 16px 0 0;
    padding: 8px 14px;
    background: #00a6c4;
    border: 1px solid rgba(255,255,255,0.5);
    border-radius: 3px;
    color: #fff;
    cursor: pointer;
  }

  select {
   -webkit-appearance: button;
   border: 1px solid rgba(255,255,255,0.5);
   color: #c9c9c9;
   overflow: hidden;
   padding: 5px 10px;
   white-space: nowrap;
   background: rgba(0,0,0,0.8);
}

  .modal {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0,0,0,0.8);
    z-index: 99;
  }

  .modal .modal-inner {
    display: block;
    max-width: 560px;
    width: 100%;
    margin: 0 auto;
  }

  .modal .modal-inner div {
    position: relative;
    padding-bottom: 75%;
    height: 0;
    margin-top: 25%;
  }

  .modal .modal-inner iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
</style>

<script>
import axios from 'axios'
import moment from 'moment'
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
export default {
    props: ['series', 'episodes', 'user', 'library'],
    data() {
      return {
        modalHidden: true,
        libraryData: {
          user_id: this.user !== null ? this.user.id : null,
          series_id: this.series.id,
          status: this.library !== null ? this.library.status : null,
          last_watched_episode: this.library !== null ? this.library.last_watched_episode : null,
          is_fav: this.library !== null ? this.library.is_fav : null,
          rating: this.library !== null ? this.library.rating : null,
        },
      }
    },
    methods: {
      formatDate(date) {
        return moment(date).format('MMMM YYYY');
      },
      addLibrary() {
        axios.post(`/series/library`, this.libraryData).then(response => {
          Turbolinks.visit(`/series/${this.series.id}`)
          console.log(this.libraryData);
          console.log(response);
        }).catch((error) => {
          console.log(error);
        });
      },
      updateLibrary() {
        if(this.libraryData.status == 2) {
          this.libraryData.last_watched_episode = this.series.total_eps;
        }
        axios.patch(`/series/library/${this.library.id}`, this.libraryData).then(response => {
          //Turbolinks.visit(`/login`)
          console.log(response);
        }).catch((error) => {
          console.log(error);
        });
      }
    },
}
</script>