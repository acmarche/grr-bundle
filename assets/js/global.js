import { reactive, readonly } from 'vue';

const state = reactive({
    count: 0,
});

const increment = function () {
    state.count += 1;
};

export default { state: readonly(state), increment };
