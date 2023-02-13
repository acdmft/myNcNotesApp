<template>
  
    <div v-if="$store.state.currentNote.active" class="input-wrapper">
      <input
        ref="title"
        v-model="$store.state.currentNote.title"
        type="text"
        :disabled="updating"
        placeholder="title"
      />
      <textarea
        ref="content"
        v-model="$store.state.currentNote.content"
        :disabled="updating"
        placeholder="note content"
      />
      <input
        type="button"
        class="primary"
        :value="t('mynotesapp', 'Save')"
        :disabled="updating || !savePossible"
        @click="handleClick"
      />
    </div>
    <div v-else id="emptycontent">
      <div class="icon-file" />
      <h2>{{ t("mynotesapp", "Hello! You can create new note!") }}</h2>
    </div>
</template>

<script>
export default {
  name: "NoteForm",
  data() {
    return {
      updating: false,
      savePossible: true,
      // currentNote: null,
    };
  },
  props: {
    // currentNote: null,
    // currentNote: null,
  },
  async mounted() {
    console.log('mounted')
    this.$nextTick(() => {
          this.$refs.title.focus();
        });
  },
  methods: {
    openNote(note) {
      if (this.updating) {
        return;
      }
      this.$store.state.currentNote.id = note.id;
      this.$nextTick(() => {
        this.$refs.content.focus();
      });
    },
    
    handleClick() {
      // crée l'objet note
      // const note = { note: "valeur", .. }
      // sauvegarde la note dans la liste note qui est partagé entre les composants => Vuex
      // this.store.notes[] = note
      this.$emit("save-note");
    },
  },
};
</script>

<style>
.input-wrapper {
  display: flex;
  width: 100%;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}
input[type="text"],
input[type="button"] {
  width: 70%;
  margin: 1rem 0;
}
input[type="button"] {
  width: 30%;
}

textarea {
  flex-grow: 1;
  width: 70%;
  margin: 1rem 0;
}
</style>
