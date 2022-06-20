export default () => {
    const format = (amount) => {
        return Math.floor(amount / 10000000000000) / 100000;
    }

    return {
        format,
    }
}
