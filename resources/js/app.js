import './bootstrap';
import Sortable from 'sortablejs';

window.iniciarOrdenacaoCurriculo = (elemento, salvar) => {
    if (elemento.dataset.ordenacaoAtiva) return;
    elemento.dataset.ordenacaoAtiva = '1';

    const ordem = () => Array.from(elemento.querySelectorAll(':scope > [data-modulo-id]')).map((modulo) => ({
        id: Number(modulo.dataset.moduloId),
        aulas: Array.from(modulo.querySelectorAll('[data-aulas] > [data-aula-id]')).map((aula) => Number(aula.dataset.aulaId)),
    }));

    Sortable.create(elemento, { handle: '.modulo-alca', animation: 150, onEnd: () => salvar(ordem()) });
    elemento.querySelectorAll('[data-aulas]').forEach((lista) => {
        Sortable.create(lista, { group: 'aulas', handle: '.aula-alca', animation: 150, onEnd: () => salvar(ordem()) });
    });
};

window.iniciarOrdenacaoMateriais = (elemento, salvar) => {
    if (elemento.dataset.ordenacaoAtiva) return;
    elemento.dataset.ordenacaoAtiva = '1';

    Sortable.create(elemento, {
        handle: '.material-alca',
        animation: 150,
        onEnd: () => salvar(Array.from(elemento.querySelectorAll('[data-material-id]')).map((item) => Number(item.dataset.materialId))),
    });
};

window.dispatchEvent(new CustomEvent('curriculo-js-pronto'));
