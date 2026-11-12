import { MinusIcon, PlusIcon } from './icons';

const boxClass =
    'flex h-11 w-11 items-center justify-center rounded-xl border border-[#5A2A18]/25 bg-[#FDFBF7] text-ink transition-colors';

const buttonClass = `${boxClass} hover:border-[#C86A4B]/60 hover:bg-[#FAF2E9] hover:text-[#C86A4B] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C86A4B]/30 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:border-[#5A2A18]/25 disabled:hover:bg-[#FDFBF7] disabled:hover:text-ink`;

export default function QuantityStepper({ quantity, stock, onChange }) {
    const atMinimum = quantity <= 1;
    const atMaximum = quantity >= stock;

    const handleInputChange = (event) => {
        const digits = event.target.value.replace(/\D/g, '');
        onChange(digits === '' ? 1 : Number(digits));
    };

    return (
        <div className="mt-7 flex items-center gap-4">
            <p className="shrink-0 text-sm font-semibold text-ink">Quantity</p>

            <div role="group" aria-label="Item quantity" className="flex items-center gap-2">
                <button
                    type="button"
                    onClick={() => onChange(quantity - 1)}
                    disabled={atMinimum}
                    aria-label="Decrease quantity"
                    className={buttonClass}
                >
                    <MinusIcon />
                </button>

                <input
                    type="text"
                    inputMode="numeric"
                    pattern="[0-9]*"
                    value={quantity}
                    onChange={handleInputChange}
                    onFocus={(event) => event.target.select()}
                    aria-label="Item quantity"
                    className="h-11 w-14 rounded-xl border border-[#5A2A18]/25 bg-[#FDFBF7] text-center text-sm font-semibold text-ink shadow-none transition-colors focus:border-[#C86A4B] focus:outline-none focus:ring-2 focus:ring-[#C86A4B]/20"
                />

                <button
                    type="button"
                    onClick={() => onChange(quantity + 1)}
                    disabled={atMaximum}
                    aria-label="Increase quantity"
                    className={buttonClass}
                >
                    <PlusIcon />
                </button>
            </div>
        </div>
    );
}
