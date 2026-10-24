// House theme for the free plot tool, merged UNDER the spec's own `config` (the spec wins).
// Fonts and sizes follow figure-style (Arial, readable at print width); the categorical palette is Okabe-Ito,
// chosen for colour-blind safety (figure-style has no categorical palette). Yellow and black come last.
export const OKABE_ITO = ['#0072B2', '#E69F00', '#009E73', '#D55E00', '#56B4E9', '#CC79A7', '#F0E442', '#000000'];

const FONT = 'Arial, Helvetica, sans-serif';

export const THEME = {
    background: '#ffffff',
    font: FONT,
    view: { stroke: null, continuousWidth: 400, continuousHeight: 260, discreteWidth: { step: 36 }, discreteHeight: { step: 28 } },
    axis: {
        labelFont: FONT, titleFont: FONT, labelFontSize: 13, titleFontSize: 14, titleFontWeight: 'normal',
        labelColor: '#000000', titleColor: '#000000', domainColor: '#000000', tickColor: '#000000',
        gridColor: '#e6e6e6', gridWidth: 1, labelPadding: 4, titlePadding: 8,
    },
    legend: { labelFont: FONT, titleFont: FONT, labelFontSize: 13, titleFontSize: 14, titleFontWeight: 'normal', orient: 'bottom', symbolSize: 90 },
    title: { font: FONT, fontSize: 15, fontWeight: 'bold', anchor: 'start', color: '#000000' },
    header: { labelFont: FONT, titleFont: FONT, labelFontSize: 13, titleFontSize: 14 },
    range: { category: OKABE_ITO },
    mark: { color: OKABE_ITO[0] },
    line: { strokeWidth: 2.5 },
    point: { size: 70, filled: true },
    text: { font: FONT, fontSize: 13 },
};
