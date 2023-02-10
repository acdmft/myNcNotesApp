<template>
  <div>
    <!-- <div class="input-wrapper"> -->
    <div v-if="currentNote" class="input-wrapper">
      <input ref="title"
        v-model="currentNote.title"
        type="text"
        :disabled="updating"
        placeholder="title"
      />
      <textarea
        ref="content"
        v-model="currentNote.content"
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
    currentNote: null,
    // currentNote: null,
  },
  methods: {
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
