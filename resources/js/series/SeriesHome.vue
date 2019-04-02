
<template>
    <layout title="Find Anime" :user="user">
      <header class="flex" style="justify-content: space-between;">
          <h1 class="text-primary"><span class="underline">Anime Series</span></h1>
          <select class="filter" v-model="filter.genre" @change="submit()">
            <option value="genre">No Filter</option>
            <option value="action">Action</option>
            <option value="adventure">Adventure</option>
            <option value="comedy">Comedy</option>
            <option value="drama">Drama</option>
            <option value="ecchi">Ecchi</option>
            <option value="fantasy">Fantasy</option>
            <option value="game">Game</option>
            <option value="magic">Magic</option>
            <option value="military">Military</option>
            <option value="mystery">Mystery</option>
            <option value="romance">Romance</option>
            <option value="school">School</option>
            <option value="sci-fi">Sci-Fi</option>
            <option value="slice of life">Slice of Life</option>
            <option value="supernatural">Supernatural</option>
            <option value="super power">Super Power</option>
          </select>
      </header>
      <div class="series-list flex flex-wrap">
        <div class="card series-card" v-for="anime in series.data" :key="anime.id">
          <a :href="`/series/${anime.id}-${ strReplace(anime.name) }`">
            <div class="thumbnail">
              <img class="lazy" :src="anime.poster" :alt="anime.name">
            </div>
            <p><span class="text-primary left">{{ anime.name }}</span></p>
          </a>
        </div>
      </div>
      <div class="pagination">
        <a class="prev" data-turbolinks-scroll="false" v-if="series.current_page !== 1" :href="series.prev_page_url"><i class="fas fa-chevron-left"></i></a> <a class="next" data-turbolinks-scroll="false" v-if="series.current_page !== series.last_page" :href="series.next_page_url"><i class="fas fa-chevron-right"></i></a>
      </div>
    </layout>
</template>

<style scoped>
.series-card {
  width: 15%;
  box-sizing: border-box;
  margin: 0 16px 1rem 0;
  position: relative;
  overflow: hidden;
}

.series-card p {
  width: 100%;
  line-height: 1.1rem;
}

select.filter {
   -webkit-appearance: button;
   border: 1px solid rgba(255,255,255,0.5);
   color: #c9c9c9;
   font-size: inherit;
   overflow: hidden;
   padding: 5px 10px;
   text-overflow: ellipsis;
   white-space: nowrap;
   background: rgba(0,0,0,0.8);
}


.thumbnail img {
    height: 220px;
    object-fit: cover;
  }

  @media screen and (max-width: 1100px) {
    .series-card {
      width: calc(25% - 16px);
    }

    .thumbnail img {
      height: 300px;
    }
  }

  @media screen and (max-width: 880px) {
    .thumbnail img {
      height: 260px;
    }
  }

  @media screen and (max-width: 780px) {
    .series-card {
      width: calc(33% - 16px);
    }

    .thumbnail img {
      height: 300px;
    }
  }

  @media screen and (max-width: 600px) {
    .series-card {
      width: calc(50% - 16px);
    }

    .thumbnail img {
      height: 300px;
    }
  }

  @media screen and (max-width: 480px) {
    .series-card {
      width: calc(100% - 8px);
    }

    .thumbnail img {
      height: auto;
    }
  }
</style>

<script>
export default {
    props: ['series', 'genre', 'user'],
    data() {
      return {
        filter: {
          genre: this.genre,
        }
      }
    },
    methods: {
        submit() {
            if(this.filter.genre !== 'genre') {
                Turbolinks.visit(`/series/genre/${this.filter.genre}`);
            } else {
              Turbolinks.visit('/series/')
            }
        },
        strReplace(string) {
          string = string.replace(/\//g, '-');
          string = string.replace(/ /g, '-');
          string = string.replace(/\./g, '-');
          string = string.replace(/\:/g, '');
          string.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g,'-');
          return string.replace(' ', '-');
        }
    }
}
</script>