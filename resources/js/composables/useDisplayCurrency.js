export default () => {
    const format = (amount) => {
        return Math.floor(amount / 1000000000000000) / 1000;
    }

    return {
        format,
    }
}
