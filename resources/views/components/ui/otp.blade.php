{{--
    Kode OTP 6 digit: kotak terpisah, auto-maju, backspace mundur, tempel (paste) semua digit,
    dan submit otomatis saat penuh (state "FILLED" di Figma). Mengisi <input type=hidden name=...>.
--}}
@props(['name' => 'code', 'length' => 6, 'error' => false, 'autosubmit' => true])
<div x-data="{
        len: {{ $length }}, digits: Array({{ $length }}).fill(''),
        get value() { return this.digits.join('') },
        focusAt(i) { this.$refs['d' + Math.max(0, Math.min(this.len - 1, i))]?.focus() },
        input(i, e) {
            const v = e.target.value.replace(/\D/g, '');
            if (v.length > 1) { this.fill(v, i); return }
            this.digits[i] = v;
            if (v && i < this.len - 1) this.focusAt(i + 1);
            this.maybeSubmit();
        },
        fill(text, start = 0) {
            const chars = text.replace(/\D/g, '').slice(0, this.len - start).split('');
            chars.forEach((c, k) => this.digits[start + k] = c);
            this.focusAt(start + chars.length);
            this.maybeSubmit();
        },
        back(i, e) { if (!this.digits[i] && i > 0) { this.digits[i - 1] = ''; this.focusAt(i - 1); e.preventDefault() } },
        maybeSubmit() { if ({{ $autosubmit ? 'true' : 'false' }} && this.value.length === this.len) this.$nextTick(() => this.$root.closest('form')?.requestSubmit()) },
    }" {{ $attributes }}>
    <input type="hidden" name="{{ $name }}" :value="value">
    <div class="grid gap-2" style="grid-template-columns: repeat({{ $length }}, minmax(0, 1fr))" role="group" aria-label="Kode verifikasi {{ $length }} digit">
        @for ($i = 0; $i < $length; $i++)
            <input type="text" inputmode="numeric" autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}" maxlength="{{ $length }}" x-ref="d{{ $i }}"
                   x-model="digits[{{ $i }}]" @input="input({{ $i }}, $event)" @keydown.backspace="back({{ $i }}, $event)"
                   @paste.prevent="fill($event.clipboardData.getData('text'), {{ $i }})" @focus="$event.target.select()"
                   aria-label="Digit {{ $i + 1 }}" @if ($i === 0) autofocus @endif
                   class="tnum aspect-[4/5] w-full rounded-xl border-0 bg-surface p-0 text-center font-mono text-2xl font-semibold text-ink shadow-inset ring-1 ring-inset transition focus:ring-2 {{ $error ? 'ring-brick-600 focus:ring-brick-600' : 'ring-line hover:ring-line-strong focus:ring-brand-600' }}">
        @endfor
    </div>
</div>
