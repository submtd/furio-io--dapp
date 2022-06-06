export default () => {
    const format = (amount) => {
        return Math.floor(amount / BigInt("1000000000000000")) / 1000;
    }

    return {
        format,
    }
}
