<template>
    <layout title="Profile" :user="user">
      <section class="profile">
        <header>
          <h1 class="text-primary"><span class="underline">{{ profile.username }}'s Profile</span></h1>
          <ul class="tabs">
            <li class="tab" :class="{ active: library }" @click="library = true; recent = false;">Library</li>
            <li class="tab" :class="{ active: recent }" @click="library = false; recent = true;">Recently Watched</li>
          </ul>
        </header>
        <div class="library-list" v-if="library">
          <div class="library-card" v-for="library in profile.library" :key="library.id">
            <a :href="`/series/${library.series.id}-${library.series.slug}`">
              <div class="thumbnail">
                <img :src="library.series.poster">
              </div>
            </a>
            <div class="info">
              <h3><a :href="`/series/${library.series.id}-${library.series.slug}`">{{ library.series.name }}</a></h3>
              <p>{{ library.status }}</p>
              <p>Episodes: {{ library.last_watched_episode }}/{{ library.series.total_eps }}</p>
              <div class="fav">
                <i class="far fa-star" v-if="!library.is_fav"></i><i class="fas fa-star" v-if="library.is_fav"></i>
              </div>
              <div class="rating">
                <p>{{ library.rating }}/10</p>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="recent" v-if="recent">
        <div class="recent-list">
          <div class="recent-card" v-for="recent in profile.recently_watched" :key="recent.id">
            <a :href="`/series/${recent.series.id}-${recent.series.slug}`">
              <div class="thumbnail">
                <img :src="recent.series.poster">
              </div>
            </a>
            <div class="info">
              <h3><a :href="`/series/${recent.series.id}-${recent.series.slug}`">{{ recent.series.name }}</a></h3>
              <p>Watched: {{ recent.time }}<p>
              <p>Episode: {{ recent.episode }}</p>
            </div>
          </div>
        </div>
      </section>
    </layout>
</template>

<style scoped>
  .tabs {
    margin: 30px 0;
  }

  .tabs .tab {
    display: inline-block;
    margin: 0 16px 0 0;
    padding: 8px 14px;
    background: rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.5);
    border-radius: 3px;
    cursor: pointer;
  }

  .tabs .tab.active {
    background: #00a6c4;
    color: #fff;
  }

  i.fa-star {
    color: #FFBA08;
  }

  .library-card,
  .recent-card {
    display: flex;
    background: rgba(255,255,255,0.1);
    padding: 8px;
    position: relative;
  }

  .library-card:nth-child(even),
  .recent-card:nth-child(even) {
    background: none;
  }

  .thumbnail {
    max-width: 80px;
  }

  .info {
    align-self: center;
    width: 100%;
    padding-left: 16px;
  }

  .info .fav {
    position: absolute;
    top: 16px;
    right: 16px;
  }

  .info .rating {
    position: absolute;
    top: 16px;
    right: 45px;
  }

  .info .rating p {
    line-height: 1;
  }

  .info h3 a {
    color: #fff;
    line-height: 1.5rem;
  }
</style>

<script>
export default {
    props: ['profile', 'user'],
    data() {
      return {
        library: true,
        recent: false,
      }
    }
}
</script>