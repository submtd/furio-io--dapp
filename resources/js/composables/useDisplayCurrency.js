export default () => {
    const format = (amount) => {
        return Math.floor(BigInt(amount) / BigInt("1000000000000000")) / BigInt("1000");
    }

    return {
        format,
    }
}
