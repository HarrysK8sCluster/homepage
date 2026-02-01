const C_CODE_POOL = [
`typedef struct {
    uint32_t id;
    char name[32];
    uint64_t last_seen_ms;
    bool online;
} device_t;`,
    `static uint64_t now_ms(void) {
    static uint64_t t = 0;
    return (t += 100);
}`, `static void device_set_name(device_t *d, const char *name) {
    if (!d || !name) return;
    strncpy(d->name, name, sizeof(d->name) - 1);
    d->name[sizeof(d->name) - 1] = '\\0';
}`, `static bool buf_append(buffer_t *b, const void *src, size_t n) {
    if (!b || !src || n == 0) return true;
    if (!buf_reserve(b, b->len + n)) return false;
    memcpy(b->data + b->len, src, n);
    b->len += n;
    return true;
}`, `#define RB_CAP 256
typedef struct {
    uint8_t data[RB_CAP];
    size_t head;
    size_t tail;
} ringbuf_t;`, `static uint32_t crc32_step(uint32_t crc, uint8_t b) {
    crc ^= b;
    for (int i = 0; i < 8; i++) {
        uint32_t mask = -(crc & 1u);
        crc = (crc >> 1) ^ (0xEDB88320u & mask);
    }
    return crc;
}`, `static uint32_t crc32(const void *data, size_t n) {
    const uint8_t *p = (const uint8_t*)data;
    uint32_t crc = 0xFFFFFFFFu;
    for (size_t i = 0; i < n; i++) crc = crc32_step(crc, p[i]);
    return ~crc;
}`, `static void buf_free(buffer_t *b) {
    if (!b) return;
    free(b->data);
    b->data = NULL;
    b->len = 0;
    b->cap = 0;
}`, `static bool buf_reserve(buffer_t *b, size_t n) {
    if (!b) return false;
    if (b->cap >= n) return true;

    size_t new_cap = b->cap ? b->cap : 64;
    while (new_cap < n) new_cap *= 2;

    void *p = realloc(b->data, new_cap);
    if (!p) return false;

    b->data = (uint8_t*)p;
    b->cap = new_cap;
    return true;
}`
];
